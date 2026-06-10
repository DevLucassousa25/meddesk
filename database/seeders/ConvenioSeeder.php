<?php

namespace Database\Seeders;

use App\Models\Convenio;
use Illuminate\Database\Seeder;

class ConvenioSeeder extends Seeder
{
    public function run(): void
    {
        $convenios = [
            ['nome' => 'Particular',        'codigo' => 'PART',    'registro_ans' => null],
            ['nome' => 'Unimed',            'codigo' => 'UNIMED',  'registro_ans' => '301485'],
            ['nome' => 'Bradesco Saúde',    'codigo' => 'BRADESCO','registro_ans' => '005711'],
            ['nome' => 'Amil',              'codigo' => 'AMIL',    'registro_ans' => '326305'],
            ['nome' => 'SulAmérica',        'codigo' => 'SULAMÉR', 'registro_ans' => '006246'],
            ['nome' => 'Hapvida',           'codigo' => 'HAPVIDA', 'registro_ans' => '368253'],
            ['nome' => 'Notre Dame Intermédica', 'codigo' => 'NDI', 'registro_ans' => '359017'],
            ['nome' => 'Cassems',           'codigo' => 'CASSEMS', 'registro_ans' => '326330'],
            ['nome' => 'Prevent Senior',    'codigo' => 'PREVENT', 'registro_ans' => '411221'],
            ['nome' => 'Porto Seguro Saúde','codigo' => 'PORTO',   'registro_ans' => '393321'],
        ];

        foreach ($convenios as $convenio) {
            Convenio::firstOrCreate(
                ['nome' => $convenio['nome']],
                array_merge($convenio, ['ativo' => true])
            );
        }
    }
}
