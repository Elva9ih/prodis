<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            // Add remarks column
            $table->text('remarks')->nullable()->after('whatsapp_number');

            // Make bankili_number nullable (keeping for backward compatibility)
            $table->string('bankili_number', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->string('bankili_number', 20)->nullable(false)->change();
        });
    }
};
