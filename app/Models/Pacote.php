<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pacote extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'valor_bruto',
        'tipo_desconto',
        'desconto',
        'valor_final',
        'validade_dias',
        'unidade_id',
        'ativo',
    ];

    protected $casts = [
        'valor_bruto'  => 'decimal:2',
        'desconto'     => 'decimal:2',
        'valor_final'  => 'decimal:2',
        'ativo'        => 'boolean',
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(PacoteItem::class);
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Calcula e retorna o valor final após aplicar o desconto informado.
     */
    public static function calcularFinal(float $bruto, string $tipoDesconto, float $desconto): float
    {
        if ($tipoDesconto === 'percentual') {
            return max(0, $bruto - ($bruto * $desconto / 100));
        }

        return max(0, $bruto - $desconto);
    }
}
