<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->text('description')->nullable()->after('supplier_id');
            $table->date('due_date')->nullable()->after('description');
            $table->decimal('amount_paid', 15, 2)->default(0)->after('total_amount');
        });

        // Normalize legacy statuses to the new naming.
        DB::table('bills')->whereIn('status', ['draft', 'sent'])->update(['status' => 'unpaid']);
        DB::table('bills')->where('status', 'overdue')->update(['status' => 'unpaid']);

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE bills DROP CONSTRAINT IF EXISTS bills_status_check");
            DB::statement("ALTER TABLE bills ADD CONSTRAINT bills_status_check CHECK (status IN ('unpaid', 'partial', 'paid', 'cancelled'))");
        } else {
            Schema::table('bills', function (Blueprint $table) {
                $table->enum('status', ['unpaid', 'partial', 'paid', 'cancelled'])->default('unpaid')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE bills DROP CONSTRAINT IF EXISTS bills_status_check");
            DB::statement("ALTER TABLE bills ADD CONSTRAINT bills_status_check CHECK (status IN ('draft', 'unpaid', 'paid', 'overdue', 'cancelled'))");
        } else {
            Schema::table('bills', function (Blueprint $table) {
                $table->enum('status', ['draft', 'unpaid', 'paid', 'overdue', 'cancelled'])->default('draft')->change();
            });
        }

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['description', 'due_date', 'amount_paid']);
        });
    }
};
