<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', [
                'credit_card', 'mobile_money', 'cash', 'bank_transfer'
            ]);
            $table->enum('status', [
                'pending', 'completed', 'failed', 'refunded'
            ])->default('pending');
            $table->string('transaction_id')->unique()->nullable();
            $table->text('payment_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};