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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('objective', ['branding', 'traffic', 'conversion'])->default('branding');
            $table->decimal('amount', 10, 2); // Valor total da campanha
            $table->decimal('min_amount', 10, 2)->default(200.00); // Valor mínimo R$ 200
            $table->decimal('influencer_amount', 10, 2)->nullable(); // Valor que vai para o influencer (60%)
            $table->decimal('gopubli_commission', 10, 2)->nullable(); // Comissão GO Publi (20%)
            $table->decimal('marketing_budget', 10, 2)->nullable(); // Orçamento de marketing (20%)
            $table->enum('status', ['draft', 'open', 'in_progress', 'completed', 'cancelled', 'blocked'])->default('draft');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->boolean('blocked')->default(false); // Bloqueada por inadimplência
            $table->text('blocked_reason')->nullable();
            $table->foreignId('selected_influencer_id')->nullable()->constrained('influencers')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('company_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('selected_influencer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
