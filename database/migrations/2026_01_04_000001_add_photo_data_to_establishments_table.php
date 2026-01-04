<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds photo_data column to store base64 images directly in database
     * This removes the need for persistent file storage on Railway free plan
     */
    public function up(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            // Store compressed base64 images directly in database
            // Using TEXT type which can store up to ~65KB (enough for compressed thumbnails)
            // photos_data stores JSON array of {data: base64, label: string}
            $table->text('photo_data')->nullable()->after('photo');
            $table->json('photos_data')->nullable()->after('photos_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn(['photo_data', 'photos_data']);
        });
    }
};
