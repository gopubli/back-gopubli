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
        Schema::create('terms_acceptances', function (Blueprint $table) {
            $table->id();
            $table->morphs('user'); // Pode ser Company ou Influencer - já cria índice
            $table->string('term_type'); // confidentiality, privacy_policy, terms_of_use
            $table->string('term_version')->default('1.0');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('accepted_at');
            $table->timestamps();

            // Índice adicional
            $table->index('term_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_acceptances');
    }
};
