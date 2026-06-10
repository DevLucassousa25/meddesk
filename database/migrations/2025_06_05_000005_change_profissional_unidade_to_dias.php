<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissional_unidade', function (Blueprint $table) {
            $table->dropColumn(['horario_inicio', 'horario_fim']);
            $table->json('dias')->nullable()->after('unidade');
        });
    }

    public function down(): void
    {
        Schema::table('profissional_unidade', function (Blueprint $table) {
            $table->dropColumn('dias');
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
        });
    }
};
