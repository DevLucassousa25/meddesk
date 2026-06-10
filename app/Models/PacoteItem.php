<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacoteItem extends Model
{
    protected $table = 'pacote_itens';

    protected $fillable = [
        'pacote_id',
        'tabela_preco_id',
        'quantidade',
        'valor_unitario',
        'subtotal',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class);
    }

    public function tabelaPreco(): BelongsTo
    {
        return $this->belongsTo(TabelaPreco::class);
    }
}
