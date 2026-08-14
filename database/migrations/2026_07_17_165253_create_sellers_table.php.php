<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->text('business_address')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_ratings')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};