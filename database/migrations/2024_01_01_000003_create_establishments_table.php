<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['client', 'fournisseur']);
            $table->string('name');
            $table->string('owner_name');
            $table->string('phone_country_code', 5);
            $table->string('phone_number', 20);
            $table->string('whatsapp_country_code', 5)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('bankili_number', 20);

            // Location fields - IMMUTABLE after creation
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('location_accuracy', 8, 2)->nullable();
            $table->timestamp('captured_at');

            $table->timestamp('synced_at')->useCurrent();
            $table->timestamps();

            $table->index(['agent_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index('synced_at');
        });

        Schema::create('establishment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->string('question_code');
            $table->string('answer_code');
            $table->timestamps();

            $table->index(['establishment_id', 'question_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establishment_answers');
        Schema::dropIfExists('establishments');
    }
};
