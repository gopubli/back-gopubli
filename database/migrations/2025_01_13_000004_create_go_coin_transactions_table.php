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
        Schema::create('go_coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('go_coin_wallets')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']); // crédito ou débito
            $table->decimal('amount', 10, 2);
            $table->string('category'); // campaign_bonus, redemption, marketing_service, etc
            $table->text('description');
            $table->morphs('related'); // Relacionamento polimórfico (Campaign, etc) - já cria índice
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->timestamps();

            // Índices adicionais
            $table->index('wallet_id');
            $table->index('type');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('go_coin_transactions');
    }
};
