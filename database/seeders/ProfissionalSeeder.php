<?php

namespace Database\Seeders;

use App\Models\Profissional;
use Illuminate\Database\Seeder;

class ProfissionalSeeder extends Seeder
{
    public function run(): void
    {
        $profissionais = [
            ['nome' => 'Dr. Carlos Mendes',   'especialidade' => 'Clínica Geral',       'crm' => 'CRM/SP 123456'],
            ['nome' => 'Dra. Ana Souza',       'especialidade' => 'Cardiologia',        'crm' => 'CRM/SP 234567'],
            ['nome' => 'Dr. Pedro Alves',      'especialidade' => 'Ortopedia',          'crm' => 'CRM/SP 345678'],
            ['nome' => 'Dra. Mariana Costa',   'especialidade' => 'Dermatologia',       'crm' => 'CRM/SP 456789'],
            ['nome' => 'Dr. Felipe Ribeiro',   'especialidade' => 'Neurologia',         'crm' => 'CRM/SP 567890'],
        ];

        foreach ($profissionais as $prof) {
            Profissional::firstOrCreate(
                ['nome' => $prof['nome']],
                array_merge($prof, ['ativo' => true])
            );
        }
    }
}
