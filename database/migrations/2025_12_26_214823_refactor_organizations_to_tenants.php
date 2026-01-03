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
        // 1. Rename organizations to tenants
        Schema::rename('organizations', 'tenants');

        // 2. Add status to tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('status')->default('TRIAL')->after('default_currency'); // TRIAL, ACTIVE, EXPIRED
        });

        // 3. Rename organization_id to tenant_id in all related tables
        $tables = [
            'users',
            'accounts',
            'beneficiaries',
            'transactions',
            'exchange_rates',
            'access_logs',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign key if it exists (assuming laravel convention or previous migration)
                // In some cases we might need to know the foreign key name
                // For safety in this environment, we just rename the column if it exists
                if (Schema::hasColumn($tableName, 'organization_id')) {
                    $table->renameColumn('organization_id', 'tenant_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'accounts',
            'beneficiaries',
            'transactions',
            'exchange_rates',
            'access_logs',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'tenant_id')) {
                    $table->renameColumn('tenant_id', 'organization_id');
                }
            });
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::rename('tenants', 'organizations');
    }
};
