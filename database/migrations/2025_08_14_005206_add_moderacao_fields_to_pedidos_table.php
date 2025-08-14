<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->text('motivo_recusa')->nullable()->after('status');
            $table->foreignId('secretario_id')
                  ->nullable()
                  ->after('motivo_recusa')
                  ->constrained('secretarios')
                  ->nullOnDelete();
            $table->timestamp('avaliado_em')->nullable()->after('secretario_id');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['secretario_id']);
            $table->dropColumn(['motivo_recusa', 'secretario_id', 'avaliado_em']);
        });
    }
};
