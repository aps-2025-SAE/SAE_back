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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->decimal('valor', 10, 2);
            $table->text('descricao');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->unsignedInteger('numOfertasDiarias');
            $table->timestamps();

            $table->unique(['tipo', 'data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
