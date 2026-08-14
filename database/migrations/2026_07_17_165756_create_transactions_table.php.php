<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', [
                'commission', 'subscription', 'service_fee', 'payout'
            ]);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('description')->nullable();
            $table->string('reference')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};