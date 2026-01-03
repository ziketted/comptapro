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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('cashbox_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['INCOME', 'EXPENSE', 'EXCHANGE']);
            $table->decimal('original_amount', 15, 2);
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->decimal('exchange_rate_used', 15, 6);
            $table->decimal('converted_amount', 15, 2); // Base currency value at time of operation
            
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            
            $table->enum('status', ['PENDING', 'VALIDATED', 'REJECTED'])->default('PENDING');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            
            // For EXCHANGE (internal transfers)
            $table->foreignId('target_cashbox_id')->nullable()->constrained('cashboxes')->onDelete('set null');
            
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['cashbox_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
