<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->string('codigo', 9)->unique()->nullable();
            $table->string('nome');
            $table->string('cpf', 14)->nullable();
            $table->string('cnpj', 18)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('genero', 20)->nullable(); // masculino, feminino, outro
            $table->date('data_nascimento')->nullable();
            $table->string('foto')->nullable(); // storage path

            // Status
            $table->string('status_cliente', 30)->default('ativo'); // ativo, inativo, prospect
            $table->boolean('ativo')->default(true);
            $table->date('data_ativacao')->nullable();
            $table->date('data_inativacao')->nullable();

            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();

            // Contato
            $table->string('telefone1', 20)->nullable();
            $table->string('celular1', 20)->nullable();
            $table->string('celular2', 20)->nullable();
            $table->string('email')->nullable();

            // Empresa / Vínculo
            $table->string('empresa')->nullable();
            $table->foreignId('tipo_vinculo_id')->nullable()->constrained('tipos_vinculo')->nullOnDelete();

            // Profissional e Convênio
            $table->foreignId('profissional_responsavel_id')->nullable()->constrained('profissionais')->nullOnDelete();
            $table->foreignId('convenio_id')->nullable()->constrained('convenios')->nullOnDelete();

            // WhatsApp / Mensagens
            $table->boolean('enviar_whatsapp')->default(false);
            $table->string('mensagens_por', 20)->nullable(); // whatsapp, sms, email
            $table->boolean('whatsapp_ativo')->default(false);
            $table->boolean('sms_ativo')->default(false);
            $table->boolean('numero_internacional')->default(false);

            // Situação Financeira
            $table->boolean('inadimplente')->default(false);
            $table->boolean('tem_guia_finalizada')->default(false);
            $table->boolean('devendo_guia')->default(false);

            // Flags
            $table->boolean('matricula_trancada')->default(false);
            $table->boolean('controle_matricula')->default(false);
            $table->boolean('financeiro_pendente')->default(false);
            $table->boolean('atencao_informacoes')->default(false);

            // Plano
            $table->date('inicio_plano')->nullable();
            $table->date('fim_plano')->nullable();

            // Observações
            $table->text('informacoes_iniciais')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
