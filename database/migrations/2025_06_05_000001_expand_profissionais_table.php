<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            // Identificação
            $table->string('identificacao', 100)->nullable()->after('nome');
            $table->string('senha_celular', 100)->nullable()->after('identificacao');
            $table->string('cpf', 18)->nullable()->after('senha_celular');
            $table->string('rg', 20)->nullable()->after('cpf');
            $table->string('conselho_profissional', 100)->nullable()->after('rg');

            // Endereço
            $table->string('cep', 9)->nullable()->after('conselho_profissional');
            $table->string('endereco')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('uf', 2)->nullable()->after('cidade');

            // Contato
            $table->string('celular1', 20)->nullable()->after('uf');
            $table->string('celular2', 20)->nullable()->after('celular1');
            $table->date('data_nascimento')->nullable()->after('celular2');

            // Agenda
            $table->integer('ordem_agenda')->default(1)->after('data_nascimento');
            $table->boolean('pode_estender_horarios')->default(false)->after('ordem_agenda');
            $table->boolean('horarios_flexiveis')->default(false)->after('pode_estender_horarios');
            $table->boolean('receber_lembrete_evoluir')->default(false)->after('horarios_flexiveis');

            // Comissão
            $table->boolean('comissao_personalizada')->default(false)->after('receber_lembrete_evoluir');
            $table->decimal('percentual_comissao', 5, 2)->nullable()->after('comissao_personalizada');

            // Permissões App Celular
            $table->boolean('perm_ver_somente_seus')->default(false)->after('percentual_comissao');
            $table->boolean('perm_fluxo_caixa')->default(false)->after('perm_ver_somente_seus');
            $table->boolean('perm_agendar_celular')->default(false)->after('perm_fluxo_caixa');
            $table->boolean('perm_editar_agenda')->default(false)->after('perm_agendar_celular');
            $table->boolean('perm_alterar_status')->default(false)->after('perm_editar_agenda');
            $table->boolean('perm_acessar_cadastro')->default(false)->after('perm_alterar_status');
            $table->boolean('perm_editar_recebimentos')->default(false)->after('perm_acessar_cadastro');
            $table->boolean('perm_remover_recebimentos')->default(false)->after('perm_editar_recebimentos');

            // Foto
            $table->string('foto')->nullable()->after('perm_remover_recebimentos');
        });
    }

    public function down(): void
    {
        Schema::table('profissionais', function (Blueprint $table) {
            $table->dropColumn([
                'identificacao','senha_celular','cpf','rg','conselho_profissional',
                'cep','endereco','numero','bairro','cidade','uf',
                'celular1','celular2','data_nascimento',
                'ordem_agenda','pode_estender_horarios','horarios_flexiveis','receber_lembrete_evoluir',
                'comissao_personalizada','percentual_comissao',
                'perm_ver_somente_seus','perm_fluxo_caixa','perm_agendar_celular','perm_editar_agenda',
                'perm_alterar_status','perm_acessar_cadastro','perm_editar_recebimentos','perm_remover_recebimentos',
                'foto',
            ]);
        });
    }
};
