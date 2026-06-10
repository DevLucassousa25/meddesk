<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabela_precos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['consulta', 'exame', 'atendimento']);
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->decimal('valor', 10, 2)->default(0);
            $table->foreignId('especialidade_id')->nullable()->constrained('especialidades')->nullOnDelete();
            $table->foreignId('unidade_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->unsignedSmallInteger('duracao_minutos')->nullable();
            $table->string('codigo', 50)->nullable()->comment('Código interno ou TUSS/CBHPM');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabela_precos');
    }
};
