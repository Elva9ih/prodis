<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\EstablishmentAnswer;
use App\Models\SyncLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class SyncController extends Controller
{
    /**
     * Sync establishments from mobile app
     * Batch reception with full validation
     */
    public function syncEstablishments(Request $request): JsonResponse
    {
        $request->validate([
            'establishments' => 'required|array|min:1|max:100',
        ]);

        $user = $request->user();
        $establishments = $request->input('establishments');

        $syncedUuids = [];
        $failed = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($establishments as $index => $data) {
            $validation = $this->validateEstablishment($data);

            if ($validation->fails()) {
                $failed[] = [
                    'uuid' => $data['uuid'] ?? "index_$index",
                    'errors' => $validation->errors()->toArray(),
                ];
                $failedCount++;
                continue;
            }

            // Check for duplicate UUID
            if (Establishment::where('uuid', $data['uuid'])->exists()) {
                // Already synced, consider it a success
                $syncedUuids[] = $data['uuid'];
                $successCount++;
                continue;
            }

            try {
                DB::transaction(function () use ($data, $user, &$syncedUuids, &$successCount) {
                    // Store compressed photo data directly in database (no file system needed for Railway free plan)
                    $photoData = null;
                    $photosData = null;

                    // Handle single photo (backward compatibility)
                    if (!empty($data['photo'])) {
                        $photoData = $this->compressBase64Image($data['photo']);
                    }

                    // Handle multiple photos
                    if (!empty($data['photos']) && is_array($data['photos'])) {
                        $compressedPhotos = [];
                        foreach ($data['photos'] as $photo) {
                            if (!empty($photo['data'])) {
                                $compressedData = $this->compressBase64Image($photo['data']);
                                if ($compressedData) {
                                    $compressedPhotos[] = [
                                        'data' => $compressedData,
                                        'label' => $photo['label'] ?? null,
                                    ];
                                }
                            }
                        }
                        if (!empty($compressedPhotos)) {
                            $photosData = $compressedPhotos;
                            // If no main photo set, use first photo
                            if (!$photoData && isset($compressedPhotos[0])) {
                                $photoData = $compressedPhotos[0]['data'];
                            }
                        }
                    }

                    // Get city from coordinates using reverse geocoding
                    $city = $this->getCityFromCoordinates($data['latitude'], $data['longitude']);

                    $establishment = Establishment::create([
                        'uuid' => $data['uuid'],
                        'agent_id' => $user->id,
                        'type' => $data['type'],
                        'name' => $data['name'],
                        'owner_name' => $data['owner_name'],
                        'phone_country_code' => $data['phone_country_code'],
                        'phone_number' => $data['phone_number'],
                        'phones_json' => $data['phones_json'] ?? null,
                        'whatsapp_country_code' => $data['whatsapp_country_code'] ?? null,
                        'whatsapp_number' => $data['whatsapp_number'] ?? null,
                        'remarks' => $data['remarks'] ?? null,
                        'photo_data' => $photoData,
                        'photos_data' => $photosData,
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'city' => $city,
                        'location_accuracy' => $data['location_accuracy'] ?? null,
                        'captured_at' => $data['captured_at'],
                        'synced_at' => now(),
                    ]);

                    // Store answers
                    if (!empty($data['answers']) && is_array($data['answers'])) {
                        foreach ($data['answers'] as $questionCode => $answerCodes) {
                            if (is_array($answerCodes)) {
                                foreach ($answerCodes as $answerCode) {
                                    EstablishmentAnswer::create([
                                        'establishment_id' => $establishment->id,
                                        'question_code' => $questionCode,
                                        'answer_code' => $answerCode,
                                    ]);
                                }
                            }
                        }
                    }

                    $syncedUuids[] = $data['uuid'];
                    $successCount++;
                });
            } catch (\Exception $e) {
                Log::error('Sync error for UUID: ' . ($data['uuid'] ?? 'unknown'), [
                    'error' => $e->getMessage(),
                    'data' => $data,
                ]);

                $failed[] = [
                    'uuid' => $data['uuid'] ?? "index_$index",
                    'errors' => ['database' => [$e->getMessage()]],
                ];
                $failedCount++;
            }
        }

        // Log sync attempt
        SyncLog::create([
            'agent_id' => $user->id,
            'establishments_count' => count($establishments),
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'ip_address' => $request->ip(),
            'error_details' => !empty($failed) ? $failed : null,
        ]);

        return response()->json([
            'success' => $failedCount === 0,
            'synced_uuids' => $syncedUuids,
            'synced_count' => $successCount,
            'failed_count' => $failedCount,
            'failed' => $failed,
        ]);
    }

    /**
     * Validate a single establishment
     */
    private function validateEstablishment(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, [
            'uuid' => 'required|uuid',
            'type' => 'required|in:client,fournisseur',
            'name' => 'required|string|max:255',
            'owner_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\'-]+$/u', // Unicode letters, spaces, apostrophes, hyphens
            ],
            'phone_country_code' => 'required|string|max:5|regex:/^\+\d{1,4}$/',
            'phone_number' => 'required|string|max:20|regex:/^\d{6,15}$/',
            'whatsapp_country_code' => 'nullable|string|max:5|regex:/^\+\d{1,4}$/',
            'whatsapp_number' => 'nullable|string|max:20|regex:/^\d{6,15}$/',
            'remarks' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_accuracy' => 'nullable|numeric|min:0',
            'captured_at' => 'required|date',
            'answers' => 'nullable|array',
            'photo' => 'required|string', // Base64 encoded image
        ], [
            'owner_name.regex' => 'Owner name must contain only letters (Arabic or Latin), spaces, apostrophes, or hyphens.',
        ]);
    }

    /**
     * Get city name from coordinates using Nominatim (OpenStreetMap)
     * Fetches city names in multiple languages for multilingual display
     */
    private function getCityFromCoordinates(float $latitude, float $longitude): ?string
    {
        try {
            $cityNames = [];
            $languages = ['fr', 'en', 'ar'];

            foreach ($languages as $index => $lang) {
                // Respect API usage policy: max 1 request per second
                if ($index > 0) {
                    usleep(1100000); // 1.1 second delay between requests
                }

                $response = Http::timeout(5)
                    ->withHeaders([
                        'User-Agent' => 'FieldCollectionApp/1.0',
                        'Accept-Language' => $lang,
                    ])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'format' => 'json',
                        'addressdetails' => 1,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $address = $data['address'] ?? [];

                    $cityName = $address['city']
                        ?? $address['town']
                        ?? $address['village']
                        ?? $address['municipality']
                        ?? $address['county']
                        ?? $address['state']
                        ?? null;

                    if ($cityName) {
                        $cityNames[$lang] = $cityName;
                    }
                }
            }

            // Return as JSON if we have multiple languages, or single string if only one
            if (count($cityNames) > 1) {
                return json_encode($cityNames);
            } elseif (count($cityNames) === 1) {
                return array_values($cityNames)[0];
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Reverse geocoding failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process base64 image for database storage
     * Mobile app already compresses to 0.5 quality, so just validate and return
     */
    private function compressBase64Image(string $base64Data): ?string
    {
        try {
            // Ensure proper data URI format
            if (strpos($base64Data, 'data:image') === 0) {
                return $base64Data;
            }

            // Add data URI prefix if missing
            return 'data:image/jpeg;base64,' . $base64Data;
        } catch (\Exception $e) {
            Log::error('Image processing error: ' . $e->getMessage());
            return null;
        }
    }
}
