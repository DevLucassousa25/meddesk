<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tabela_precos', function (Blueprint $table) {
            $table->dropForeign(['unidade_id']);
            $table->dropColumn('unidade_id');
            $table->json('unidade_ids')->nullable()->after('especialidade_id')
                ->comment('null = disponível em todas as unidades');
        });
    }

    public function down(): void
    {
        Schema::table('tabela_precos', function (Blueprint $table) {
            $table->dropColumn('unidade_ids');
            $table->foreignId('unidade_id')->nullable()->constrained('unidades')->nullOnDelete();
        });
    }
};
