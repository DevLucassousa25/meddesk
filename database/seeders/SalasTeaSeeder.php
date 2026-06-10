<?php

namespace Database\Seeders;

use App\Models\Sala;
use Illuminate\Database\Seeder;

class SalasTeaSeeder extends Seeder
{
    public function run(): void
    {
        $salas = [
            // ── Avaliação e Diagnóstico ──────────────────────────────────────
            [
                'nome'       => 'Sala de Avaliação Diagnóstica',
                'tipo'       => 'consultorio',
                'cor'        => '#3b82f6',
                'capacidade' => 3,
                'descricao'  => 'Aplicação de instrumentos diagnósticos (ADOS-2, ADI-R, CARS). Ambiente controlado com espelho unidirecional.',
            ],
            [
                'nome'       => 'Consultório de Neuropediatria',
                'tipo'       => 'consultorio',
                'cor'        => '#6366f1',
                'capacidade' => 4,
                'descricao'  => 'Consultas médicas, avaliação neurológica e acompanhamento de desenvolvimento.',
            ],
            [
                'nome'       => 'Consultório de Psiquiatria Infantil',
                'tipo'       => 'consultorio',
                'cor'        => '#8b5cf6',
                'capacidade' => 4,
                'descricao'  => 'Avaliação psiquiátrica, manejo medicamentoso e acompanhamento familiar.',
            ],

            // ── Terapias Individuais ─────────────────────────────────────────
            [
                'nome'       => 'Sala de ABA — Terapia Comportamental',
                'tipo'       => 'procedimento',
                'cor'        => '#22c55e',
                'capacidade' => 2,
                'descricao'  => 'Sessões de Análise do Comportamento Aplicada (ABA). Mesa de trabalho, cadeiras adaptadas, materiais de DTT e NET.',
            ],
            [
                'nome'       => 'Sala de Terapia Ocupacional',
                'tipo'       => 'procedimento',
                'cor'        => '#f59e0b',
                'capacidade' => 2,
                'descricao'  => 'Intervenção em integração sensorial, habilidades de vida diária e coordenação motora fina.',
            ],
            [
                'nome'       => 'Sala de Fonoaudiologia',
                'tipo'       => 'procedimento',
                'cor'        => '#ec4899',
                'capacidade' => 2,
                'descricao'  => 'Atendimento fonoaudiológico: comunicação verbal e alternativa (CAA), deglutição e linguagem.',
            ],
            [
                'nome'       => 'Sala de Psicologia Infantil',
                'tipo'       => 'consultorio',
                'cor'        => '#0d9488',
                'capacidade' => 3,
                'descricao'  => 'Psicoterapia individual (TCC, ludoterapia), avaliação cognitiva e suporte emocional.',
            ],
            [
                'nome'       => 'Sala de Psicopedagogia',
                'tipo'       => 'procedimento',
                'cor'        => '#06b6d4',
                'capacidade' => 2,
                'descricao'  => 'Intervenção nas dificuldades de aprendizagem, escrita, leitura e habilidades acadêmicas.',
            ],
            [
                'nome'       => 'Sala de Fisioterapia',
                'tipo'       => 'procedimento',
                'cor'        => '#84cc16',
                'capacidade' => 2,
                'descricao'  => 'Desenvolvimento motor, postura, tônus muscular e coordenação motora global.',
            ],
            [
                'nome'       => 'Sala de Musicoterapia',
                'tipo'       => 'procedimento',
                'cor'        => '#f97316',
                'capacidade' => 4,
                'descricao'  => 'Intervenção com instrumentos musicais para comunicação, regulação emocional e socialização.',
            ],

            // ── Integração Sensorial ─────────────────────────────────────────
            [
                'nome'       => 'Sala Multissensorial (Snoezelen)',
                'tipo'       => 'procedimento',
                'cor'        => '#a855f7',
                'capacidade' => 3,
                'descricao'  => 'Estimulação sensorial controlada: fibras ópticas, projetor de bolhas, almofadas vibráteis, difusor aromático. Regulação sensorial e relaxamento.',
            ],
            [
                'nome'       => 'Sala de Integração Sensorial',
                'tipo'       => 'procedimento',
                'cor'        => '#14b8a6',
                'capacidade' => 3,
                'descricao'  => 'Redes, balanços, escorregadores, caixas de areia e piscina de bolinhas. Equipada para terapia de integração sensorial (IS).',
            ],

            // ── Atendimentos em Grupo ────────────────────────────────────────
            [
                'nome'       => 'Sala de Habilidades Sociais',
                'tipo'       => 'procedimento',
                'cor'        => '#f43f5e',
                'capacidade' => 8,
                'descricao'  => 'Grupos de treino de habilidades sociais (THS). Mesas redondas, espelhos, recursos visuais.',
            ],
            [
                'nome'       => 'Sala de Grupo Terapêutico',
                'tipo'       => 'procedimento',
                'cor'        => '#3b82f6',
                'capacidade' => 10,
                'descricao'  => 'Grupos terapêuticos mistos e grupos de parentalidade. Cadeiras dispostas em roda.',
            ],

            // ── Suporte à Família ────────────────────────────────────────────
            [
                'nome'       => 'Sala de Orientação Familiar',
                'tipo'       => 'reuniao',
                'cor'        => '#64748b',
                'capacidade' => 6,
                'descricao'  => 'Reuniões de devolutiva diagnóstica, orientação a pais e cuidadores, e reuniões multidisciplinares.',
            ],

            // ── Suporte Operacional ──────────────────────────────────────────
            [
                'nome'       => 'Recepção e Acolhimento',
                'tipo'       => 'recepcao',
                'cor'        => '#94a3b8',
                'capacidade' => 10,
                'descricao'  => 'Ambiente acolhedor com elementos visuais de apoio. Espaço calmo para reduzir ansiedade na espera.',
            ],
            [
                'nome'       => 'Sala de Espera Sensorial',
                'tipo'       => 'espera',
                'cor'        => '#cbd5e1',
                'capacidade' => 8,
                'descricao'  => 'Sala de espera adaptada: baixa estimulação sonora e visual, iluminação regulável, brinquedos sensoriais e cadeiras confortáveis.',
            ],
        ];

        foreach ($salas as $sala) {
            Sala::firstOrCreate(
                ['nome' => $sala['nome']],
                array_merge($sala, ['ativo' => true])
            );
        }

        $this->command->info('✅ ' . count($salas) . ' salas para clínica TEA/Autismo criadas com sucesso!');
    }
}
