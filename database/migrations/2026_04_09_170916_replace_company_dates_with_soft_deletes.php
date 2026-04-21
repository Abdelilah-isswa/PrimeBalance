<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('companies', 'deleted_at')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasColumn('companies', 'end_date')) {
            // Backfill deleted_at from prior deactivation date (end_date)
            DB::statement("UPDATE companies SET deleted_at = end_date::timestamp WHERE end_date IS NOT NULL AND deleted_at IS NULL");
        }

        $shouldDropStartDate = Schema::hasColumn('companies', 'start_date');
        $shouldDropEndDate = Schema::hasColumn('companies', 'end_date');

        if ($shouldDropStartDate || $shouldDropEndDate) {
            Schema::table('companies', function (Blueprint $table) use ($shouldDropStartDate, $shouldDropEndDate) {
                if ($shouldDropStartDate) {
                    $table->dropColumn('start_date');
                }
                if ($shouldDropEndDate) {
                    $table->dropColumn('end_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $shouldAddStartDate = !Schema::hasColumn('companies', 'start_date');
        $shouldAddEndDate = !Schema::hasColumn('companies', 'end_date');

        if ($shouldAddStartDate || $shouldAddEndDate) {
            Schema::table('companies', function (Blueprint $table) use ($shouldAddStartDate, $shouldAddEndDate) {
                if ($shouldAddStartDate) {
                    $table->date('start_date')->nullable();
                }
                if ($shouldAddEndDate) {
                    $table->date('end_date')->nullable();
                }
            });
        }

        if (Schema::hasColumn('companies', 'deleted_at') && Schema::hasColumn('companies', 'end_date')) {
            DB::statement("UPDATE companies SET end_date = deleted_at::date WHERE deleted_at IS NOT NULL AND end_date IS NULL");
        }

        if (Schema::hasColumn('companies', 'deleted_at')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
