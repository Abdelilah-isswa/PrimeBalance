<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL: Drop constraint and add the new one with 'partial'
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Drop existing check constraint if it exists
            DB::statement("ALTER TABLE invoices DROP CONSTRAINT IF EXISTS invoices_status_check");
            // Add new check constraint with 'partial' included
            DB::statement("ALTER TABLE invoices ADD CONSTRAINT invoices_status_check CHECK (status IN ('draft', 'sent', 'partial', 'paid', 'cancelled'))");
        } else {
            // For MySQL/SQLite: Modify the column directly
            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'cancelled'])->default('draft')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For PostgreSQL: Restore original constraint
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE invoices DROP CONSTRAINT IF EXISTS invoices_status_check");
            DB::statement("ALTER TABLE invoices ADD CONSTRAINT invoices_status_check CHECK (status IN ('draft', 'sent', 'paid', 'cancelled'))");
        } else {
            // For MySQL/SQLite: Modify the column back
            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft')->change();
            });
        }
    }
};
