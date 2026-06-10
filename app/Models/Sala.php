<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sala extends Model
{
    protected $table = 'salas';

    protected $fillable = [
        'unidade_id', 'nome', 'tipo', 'cor', 'capacidade', 'descricao', 'ativo',
    ];

    protected $casts = [
        'ativo'      => 'boolean',
        'capacidade' => 'integer',
    ];

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
