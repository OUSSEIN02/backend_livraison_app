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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Numéro de facture unique (ex: FAC-2025-0001)
            $table->string('invoice_number')->unique();
            
            // Clés étrangères
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Montant de la facture
            $table->decimal('amount', 10, 2)->default(0.00);
            
            // Statut de la facture
            $table->string('status')->default('en_attente'); // 'en_attente', 'payee', 'annulee'
            
            // Date d'émission de la facture
            $table->timestamp('issued_at')->nullable();
            
            // Chemin vers le fichier PDF généré (pour une future fonctionnalité d'export)
            $table->string('pdf_path')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};