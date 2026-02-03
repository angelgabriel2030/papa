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
        Schema::create('table_numero', function (Blueprint $table) {
            $table->id();
            $table->string('ip_envia', 45);
            $table->string('ip_recibe', 45);
            $table->integer('numero_enviado');
            $table->integer('numero_acumulado');
            $table->enum('estado', ['pendiente', 'recibido', 'expirado'])->default('pendiente');
            $table->timestamp('enviado_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_numero');
    }
};
