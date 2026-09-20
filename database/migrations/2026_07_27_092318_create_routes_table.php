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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('full_path')->unique();
            $table->string('type');
            $table->nullableMorphs('routable');
            $table->foreignId('parent_id')->nullable()->constrained('routes')->nullOnDelete();
            $table->string('redirect_to')->nullable();
            $table->unsignedSmallInteger('redirect_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
