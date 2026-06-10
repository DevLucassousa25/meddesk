<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProntuarioEntrada extends Model
{
    use SoftDeletes;

    protected $table = 'prontuario_entradas';

    protected $fillable = [
        'paciente_id','profissional_id','data_entrada','tipo','titulo','conteudo',
    ];

    protected $casts = ['data_entrada' => 'date'];

    public function paciente(): BelongsTo     { return $this->belongsTo(Paciente::class); }
    public function profissional(): BelongsTo  { return $this->belongsTo(Profissional::class); }

    public static function tipos(): array
    {
        return [
            'anamnese'   => 'Anamnese',
            'evolucao'   => 'Evolução',
            'prescricao' => 'Prescrição',
            'exame'      => 'Exame',
            'cirurgia'   => 'Cirurgia/Procedimento',
            'outro'      => 'Outro',
        ];
    }
}
