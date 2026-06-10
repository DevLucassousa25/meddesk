<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('profissional_id')->nullable()->constrained('profissionais')->nullOnDelete();
            $table->date('data_atendimento');
            $table->time('hora_atendimento')->nullable();
            $table->string('tipo', 50)->default('consulta'); // consulta, retorno, exame, procedimento, outro
            $table->string('status', 30)->default('realizado'); // realizado, cancelado, faltou
            $table->text('observacoes')->nullable();
            $table->string('local')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
