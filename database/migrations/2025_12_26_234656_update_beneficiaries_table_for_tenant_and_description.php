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
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (Schema::hasColumn('beneficiaries', 'organization_id')) {
                // Drop the old unique index before renaming the column
                // Assuming the index was on ['organization_id', 'name']
                $table->dropIndex(['organization_id', 'name']);
                $table->renameColumn('organization_id', 'tenant_id');
            }
            if (!Schema::hasColumn('beneficiaries', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            // Create the new unique index after renaming the column
            $table->index(['tenant_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            // Drop the new unique index before renaming the column back
            $table->dropIndex(['tenant_id', 'name']);

            $table->dropColumn('description');
            if (Schema::hasColumn('beneficiaries', 'tenant_id')) {
                $table->renameColumn('tenant_id', 'organization_id');
                // Recreate the old unique index after renaming the column back
                $table->index(['organization_id', 'name']);
            }
        });
    }
};
