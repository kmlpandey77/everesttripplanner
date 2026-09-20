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
        Schema::table('trips', function (Blueprint $table) {
            $table->json('highlights')->nullable()->after('overview');
            $table->json('detailed_itinerary')->nullable()->after('itinerary');
            $table->json('checklist')->nullable()->after('excludes');
            $table->text('know_before_you_go')->nullable()->after('checklist');
            $table->json('do_not_do')->nullable()->after('know_before_you_go');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['highlights', 'detailed_itinerary', 'checklist', 'know_before_you_go', 'do_not_do']);
        });
    }
};
