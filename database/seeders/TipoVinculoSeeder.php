<?php

namespace Database\Seeders;

use App\Models\TipoVinculo;
use Illuminate\Database\Seeder;

class TipoVinculoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Titular',
            'Dependente',
            'Responsável Financeiro',
            'Indicação',
            'Empresa Parceira',
            'Convênio Empresarial',
            'Plano Familiar',
        ];

        foreach ($tipos as $nome) {
            TipoVinculo::firstOrCreate(['nome' => $nome], ['ativo' => true]);
        }
    }
}
