<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dropRole = Schema::hasColumn('users', 'role');
        $dropStatus = Schema::hasColumn('users', 'status');
        $dropDeletedAt = Schema::hasColumn('users', 'deleted_at');

        if (!$dropRole && !$dropStatus && !$dropDeletedAt) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($dropRole, $dropStatus, $dropDeletedAt) {
            if ($dropRole) {
                $table->dropColumn('role');
            }
            if ($dropStatus) {
                $table->dropColumn('status');
            }
            if ($dropDeletedAt) {
                $table->dropSoftDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $addRole = !Schema::hasColumn('users', 'role');
        $addStatus = !Schema::hasColumn('users', 'status');
        $addDeletedAt = !Schema::hasColumn('users', 'deleted_at');

        if (!$addRole && !$addStatus && !$addDeletedAt) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($addRole, $addStatus, $addDeletedAt) {
            if ($addRole) {
                $table->string('role')->default('user');
            }
            if ($addStatus) {
                $table->string('status')->default('active');
            }
            if ($addDeletedAt) {
                $table->softDeletes();
            }
        });
    }
};
