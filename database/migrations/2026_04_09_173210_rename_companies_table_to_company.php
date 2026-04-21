<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('companies') && !Schema::hasTable('company')) {
            Schema::rename('companies', 'company');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('company') && !Schema::hasTable('companies')) {
            Schema::rename('company', 'companies');
        }
    }
};
