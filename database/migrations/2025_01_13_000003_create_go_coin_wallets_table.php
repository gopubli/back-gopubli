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
        Schema::create('go_coin_wallets', function (Blueprint $table) {
            $table->id();
            $table->morphs('holder'); // Pode ser Company ou Influencer (já cria índice automaticamente)
            $table->decimal('balance', 10, 2)->default(0); // Saldo em GO Coins
            $table->decimal('total_earned', 10, 2)->default(0); // Total ganho histórico
            $table->decimal('total_spent', 10, 2)->default(0); // Total gasto histórico
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('go_coin_wallets');
    }
};
