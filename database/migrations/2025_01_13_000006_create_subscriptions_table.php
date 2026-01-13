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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->decimal('monthly_amount', 10, 2)->default(200.00); // Mínimo R$ 200
            $table->enum('status', ['active', 'suspended', 'cancelled', 'pending'])->default('pending');
            $table->date('current_period_start')->nullable();
            $table->date('current_period_end')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->integer('days_overdue')->default(0);
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->timestamps();

            // Índices
            $table->index('company_id');
            $table->index('status');
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
