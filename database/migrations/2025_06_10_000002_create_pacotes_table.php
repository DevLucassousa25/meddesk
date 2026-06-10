<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacotes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->decimal('valor_bruto', 10, 2)->default(0)->comment('Soma dos itens sem desconto');
            $table->enum('tipo_desconto', ['percentual', 'fixo'])->default('percentual');
            $table->decimal('desconto', 10, 2)->default(0)->comment('Valor % ou R$ conforme tipo_desconto');
            $table->decimal('valor_final', 10, 2)->default(0)->comment('Valor após desconto');
            $table->unsignedSmallInteger('validade_dias')->nullable()->comment('Dias de validade após venda');
            $table->foreignId('unidade_id')->nullable()->constrained('unidades')->nullOnDelete();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('pacote_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pacote_id')->constrained('pacotes')->cascadeOnDelete();
            $table->foreignId('tabela_preco_id')->constrained('tabela_precos')->cascadeOnDelete();
            $table->unsignedSmallInteger('quantidade')->default(1);
            $table->decimal('valor_unitario', 10, 2)->comment('Snapshot do valor no momento do cadastro');
            $table->decimal('subtotal', 10, 2)->comment('quantidade * valor_unitario');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacote_itens');
        Schema::dropIfExists('pacotes');
    }
};
