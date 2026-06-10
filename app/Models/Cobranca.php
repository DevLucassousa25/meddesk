<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cobranca extends Model
{
    use SoftDeletes;

    protected $table = 'cobrancas';

    protected $fillable = [
        'paciente_id','descricao','valor','data_vencimento',
        'data_pagamento','status','forma_pagamento','observacoes',
    ];

    protected $casts = [
        'valor'           => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento'  => 'date',
    ];

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }

    public static function statusList(): array
    {
        return ['pendente'=>'Pendente','pago'=>'Pago','cancelado'=>'Cancelado','vencido'=>'Vencido'];
    }

    public static function formasPagamento(): array
    {
        return ['dinheiro'=>'Dinheiro','pix'=>'PIX','cartao_credito'=>'Cartão Crédito','cartao_debito'=>'Cartão Débito','boleto'=>'Boleto','convenio'=>'Convênio','outro'=>'Outro'];
    }

    public function getStatusComputadoAttribute(): string
    {
        if ($this->status === 'pago' || $this->status === 'cancelado') return $this->status;
        if ($this->data_vencimento->isPast()) return 'vencido';
        return $this->status;
    }
}
