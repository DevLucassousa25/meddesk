<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id','profissional_id','data_hora','duracao_minutos',
        'tipo','status','observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function paciente(): BelongsTo     { return $this->belongsTo(Paciente::class); }
    public function profissional(): BelongsTo  { return $this->belongsTo(Profissional::class); }

    public static function statusList(): array
    {
        return [
            'agendado'  => 'Agendado',
            'confirmado'=> 'Confirmado',
            'realizado' => 'Realizado',
            'cancelado' => 'Cancelado',
            'faltou'    => 'Faltou',
        ];
    }
}
