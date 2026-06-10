<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TabelaPreco extends Model
{
    protected $table = 'tabela_precos';

    protected $fillable = [
        'tipo',
        'nome',
        'descricao',
        'valor',
        'especialidade_id',
        'unidade_ids',
        'duracao_minutos',
        'codigo',
        'ativo',
    ];

    protected $casts = [
        'valor'       => 'decimal:2',
        'ativo'       => 'boolean',
        'unidade_ids' => 'array',
    ];

    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }

    /** Retorna os objetos Unidade correspondentes aos IDs salvos. */
    public function unidades()
    {
        $ids = $this->unidade_ids ?? [];
        if (empty($ids)) return collect();
        return Unidade::whereIn('id', $ids)->orderBy('nome')->get();
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDoTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public static function labelTipo(string $tipo): string
    {
        return match ($tipo) {
            'consulta'    => 'Consulta',
            'exame'       => 'Exame',
            'atendimento' => 'Atendimento',
            default       => $tipo,
        };
    }

    public static function tipos(): array
    {
        return ['consulta', 'exame', 'atendimento'];
    }
}
