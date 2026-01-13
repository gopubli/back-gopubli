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
        Schema::create('campaign_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('influencer_id')->constrained()->onDelete('cascade');
            $table->decimal('offered_amount', 10, 2)->nullable(); // Valor ofertado pelo influencer
            $table->text('proposal_message')->nullable(); // Mensagem de proposta
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('campaign_id');
            $table->index('influencer_id');
            $table->index('status');
            
            // Garantir que um influencer não se candidate duas vezes para a mesma campanha
            $table->unique(['campaign_id', 'influencer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_applications');
    }
};
