<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secretarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('login')->unique();
            $table->string('senha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secretarios');
    }
};
