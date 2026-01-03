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
        Schema::table('accounts', function (Blueprint $table) {
            // Rename and change columns
            if (Schema::hasColumn('accounts', 'name')) {
                $table->renameColumn('name', 'label');
            }
            
            // Change 'type' to 'account_type' or handle 'type' if it exists
            if (Schema::hasColumn('accounts', 'type')) {
                $table->string('type')->default('INCOME')->change();
            }

            // Default 'is_active'
            if (Schema::hasColumn('accounts', 'is_active')) {
                $table->boolean('is_active')->default(true)->change();
            }
            
            // Remove 'currency' and 'balance' columns
            if (Schema::hasColumn('accounts', 'currency')) {
                $table->dropColumn(['currency', 'balance']);
            }
            
            $table->unique(['tenant_id', 'account_number'], 'accounts_tenant_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropUnique('accounts_tenant_number_unique');
            
            if (Schema::hasColumn('accounts', 'label')) {
                $table->renameColumn('label', 'name');
            }

            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
        });
    }
};
