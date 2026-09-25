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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('bar_code')->unique()->nullable();
            $table->string('titulo');
            $table->string('autor');
            $table->unsignedInteger('existencias_totales');
            $table->unsignedInteger('existencias_disponibles');
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('nombre');
            $table->string('identificador')->unique();
            $table->string('grupo_escolar')->nullable();
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->dateTime('fecha_prestamo');
            $table->dateTime('fecha_estimada_devolucion');
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            $table->dateTime('fecha_devolucion_real')->nullable();
            $table->boolean('devuelto')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_items');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('members');
        Schema::dropIfExists('books');
    }
};
