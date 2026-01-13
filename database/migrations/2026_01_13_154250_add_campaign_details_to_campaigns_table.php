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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('category')->nullable()->after('objective');
            $table->string('platform')->nullable()->after('category');
            $table->date('start_date')->nullable()->after('platform');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('requirements')->nullable()->after('end_date');
            $table->text('deliverables')->nullable()->after('requirements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['category', 'platform', 'start_date', 'end_date', 'requirements', 'deliverables']);
        });
    }
};
