<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Especialidade extends Model
{
    protected $fillable = ['nome', 'empresa', 'unidade_ids', 'observacoes', 'ativo'];

    protected $casts = [
        'ativo'       => 'boolean',
        'unidade_ids' => 'array',
    ];

    public function profissionais(): BelongsToMany
    {
        return $this->belongsToMany(Profissional::class, 'profissional_especialidade');
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
