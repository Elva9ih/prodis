<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@fieldcollect.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create sample agents
        $agents = [
            ['name' => 'Agent Ahmed', 'email' => 'ahmed@fieldcollect.local'],
            ['name' => 'Agent Fatima', 'email' => 'fatima@fieldcollect.local'],
            ['name' => 'Agent Mohamed', 'email' => 'mohamed@fieldcollect.local'],
        ];

        foreach ($agents as $agent) {
            User::create([
                'name' => $agent['name'],
                'email' => $agent['email'],
                'password' => Hash::make('agent123'),
                'role' => 'agent',
                'is_active' => true,
            ]);
        }

        // Create questions for CLIENTS
        $servicesQuestion = Question::create([
            'code' => 'services_offered',
            'establishment_type' => 'client',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (['oil_change', 'tire_repair', 'engine_repair', 'bodywork', 'electrical', 'air_conditioning'] as $i => $code) {
            QuestionOption::create([
                'question_id' => $servicesQuestion->id,
                'code' => $code,
                'sort_order' => $i + 1,
            ]);
        }

        $vehicleTypesQuestion = Question::create([
            'code' => 'vehicle_types',
            'establishment_type' => 'client',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (['cars', 'trucks', 'motorcycles', 'buses', 'heavy_equipment'] as $i => $code) {
            QuestionOption::create([
                'question_id' => $vehicleTypesQuestion->id,
                'code' => $code,
                'sort_order' => $i + 1,
            ]);
        }

        // Create questions for FOURNISSEURS (spare parts shops)
        $partsQuestion = Question::create([
            'code' => 'parts_categories',
            'establishment_type' => 'fournisseur',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (['engine_parts', 'body_parts', 'electrical', 'tires', 'filters', 'lubricants', 'batteries'] as $i => $code) {
            QuestionOption::create([
                'question_id' => $partsQuestion->id,
                'code' => $code,
                'sort_order' => $i + 1,
            ]);
        }

        $brandsQuestion = Question::create([
            'code' => 'brands_carried',
            'establishment_type' => 'fournisseur',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (['toyota', 'nissan', 'mercedes', 'hyundai', 'renault', 'peugeot', 'kia', 'ford'] as $i => $code) {
            QuestionOption::create([
                'question_id' => $brandsQuestion->id,
                'code' => $code,
                'sort_order' => $i + 1,
            ]);
        }

        $customerTypesQuestion = Question::create([
            'code' => 'customer_types',
            'establishment_type' => 'fournisseur',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (['clients', 'individuals', 'fleet_owners', 'government'] as $i => $code) {
            QuestionOption::create([
                'question_id' => $customerTypesQuestion->id,
                'code' => $code,
                'sort_order' => $i + 1,
            ]);
        }

        // Create questions for BOTH types
        $paymentQuestion = Question::create([
            'code' => 'payment_methods',
            'establishment_type' => 'both',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        foreach (['cash', 'mobile_money', 'bank_transfer', 'credit'] as $i => $code) {
            QuestionOption::create([
                'question_id' => $paymentQuestion->id,
                'code' => $code,
                'sort_order' => $i + 1,
            ]);
        }
    }
}
