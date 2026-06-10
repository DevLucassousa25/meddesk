<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_paciente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->string('nome');
            $table->string('tipo', 50)->default('outros'); // exame, laudo, receita, guia, contrato, outros
            $table->string('arquivo'); // storage path
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamanho')->nullable(); // bytes
            $table->text('descricao')->nullable();
            $table->date('data_documento')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_paciente');
    }
};
