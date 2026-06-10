<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convenio extends Model
{
    protected $table = 'convenios';

    protected $fillable = [
        'nome',
        'codigo',
        'registro_ans',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function pacientes(): HasMany
    {
        return $this->hasMany(Paciente::class);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
