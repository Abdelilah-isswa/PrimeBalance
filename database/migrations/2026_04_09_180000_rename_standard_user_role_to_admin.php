<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // company_user.role
        DB::statement("ALTER TABLE company_user DROP CONSTRAINT IF EXISTS company_user_role_check");
        DB::statement("UPDATE company_user SET role = 'admin' WHERE role = 'standard_user'");
        DB::statement("ALTER TABLE company_user ADD CONSTRAINT company_user_role_check CHECK (role IN ('owner', 'admin', 'accountant', 'viewer'))");

        // invitations.role
        DB::statement("ALTER TABLE invitations DROP CONSTRAINT IF EXISTS invitations_role_check");
        DB::statement("UPDATE invitations SET role = 'admin' WHERE role = 'standard_user'");
        DB::statement("ALTER TABLE invitations ADD CONSTRAINT invitations_role_check CHECK (role IN ('owner', 'admin', 'accountant', 'viewer'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE invitations DROP CONSTRAINT IF EXISTS invitations_role_check");
        DB::statement("UPDATE invitations SET role = 'standard_user' WHERE role = 'admin'");
        DB::statement("ALTER TABLE invitations ADD CONSTRAINT invitations_role_check CHECK (role IN ('owner', 'accountant', 'standard_user', 'viewer'))");

        DB::statement("ALTER TABLE company_user DROP CONSTRAINT IF EXISTS company_user_role_check");
        DB::statement("UPDATE company_user SET role = 'standard_user' WHERE role = 'admin'");
        DB::statement("ALTER TABLE company_user ADD CONSTRAINT company_user_role_check CHECK (role IN ('owner', 'accountant', 'standard_user', 'viewer'))");
    }
};
