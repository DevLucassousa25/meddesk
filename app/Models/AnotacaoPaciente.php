<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnotacaoPaciente extends Model
{
    use SoftDeletes;

    protected $table = 'anotacoes_paciente';

    protected $fillable = ['paciente_id','titulo','conteudo','cor','fixada'];

    protected $casts = ['fixada' => 'boolean'];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }

    public static function cores(): array
    {
        return ['blue'=>'Azul','green'=>'Verde','yellow'=>'Amarelo','red'=>'Vermelho','purple'=>'Roxo'];
    }
}
