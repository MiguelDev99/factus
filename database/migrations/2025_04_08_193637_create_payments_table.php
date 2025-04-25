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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Para saber quién hizo el pago
            $table->string('payment_method');
            $table->string('payment_status', 1);
            $table->string('bill_number')->nullable(); // Número de factura
            $table->decimal('total', 10, 2)->default(0); // Total de la factura
            $table->timestamp('billed_at')->nullable(); // Fecha/hora de facturación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
