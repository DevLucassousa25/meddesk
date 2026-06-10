<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atendimento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id','profissional_id','data_atendimento','hora_atendimento',
        'tipo','status','observacoes','local',
    ];

    protected $casts = [
        'data_atendimento' => 'date',
    ];

    public function paciente(): BelongsTo    { return $this->belongsTo(Paciente::class); }
    public function profissional(): BelongsTo { return $this->belongsTo(Profissional::class); }

    public static function tipos(): array
    {
        return ['consulta'=>'Consulta','retorno'=>'Retorno','exame'=>'Exame','procedimento'=>'Procedimento','outro'=>'Outro'];
    }

    public static function statusList(): array
    {
        return ['realizado'=>'Realizado','cancelado'=>'Cancelado','faltou'=>'Faltou'];
    }
}
