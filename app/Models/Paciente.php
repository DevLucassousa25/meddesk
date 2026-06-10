<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'codigo',
        'nome',
        'cpf',
        'cnpj',
        'rg',
        'genero',
        'data_nascimento',
        'foto',
        'status_cliente',
        'ativo',
        'data_ativacao',
        'data_inativacao',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'telefone1',
        'celular1',
        'celular2',
        'email',
        'empresa',
        'tipo_vinculo_id',
        'profissional_responsavel_id',
        'convenio_id',
        'enviar_whatsapp',
        'mensagens_por',
        'whatsapp_ativo',
        'sms_ativo',
        'numero_internacional',
        'inadimplente',
        'tem_guia_finalizada',
        'devendo_guia',
        'matricula_trancada',
        'controle_matricula',
        'financeiro_pendente',
        'atencao_informacoes',
        'inicio_plano',
        'fim_plano',
        'informacoes_iniciais',
    ];

    protected $casts = [
        'data_nascimento'   => 'date',
        'data_ativacao'     => 'date',
        'data_inativacao'   => 'date',
        'inicio_plano'      => 'date',
        'fim_plano'         => 'date',
        'ativo'             => 'boolean',
        'enviar_whatsapp'   => 'boolean',
        'whatsapp_ativo'    => 'boolean',
        'sms_ativo'         => 'boolean',
        'numero_internacional'   => 'boolean',
        'inadimplente'           => 'boolean',
        'tem_guia_finalizada'    => 'boolean',
        'devendo_guia'           => 'boolean',
        'matricula_trancada'     => 'boolean',
        'controle_matricula'     => 'boolean',
        'financeiro_pendente'    => 'boolean',
        'atencao_informacoes'    => 'boolean',
    ];

    // ── Relationships ───────────────────────────────────────────────────────

    public function convenio(): BelongsTo
    {
        return $this->belongsTo(Convenio::class);
    }

    public function tipoVinculo(): BelongsTo
    {
        return $this->belongsTo(TipoVinculo::class, 'tipo_vinculo_id');
    }

    public function profissionalResponsavel(): BelongsTo
    {
        return $this->belongsTo(Profissional::class, 'profissional_responsavel_id');
    }

    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    // ── Accessors ───────────────────────────────────────────────────────────

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento
            ? $this->data_nascimento->age
            : null;
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Gera um código único de 9 dígitos que não se repete.
     * Tenta sequencial primeiro; se houver colisão (race condition), incrementa.
     */
    public static function gerarCodigo(): string
    {
        do {
            // Pega o maior código numérico já registrado e soma 1
            // Usa CAST para BIGINT — compatível com PostgreSQL e MySQL
            $maior = static::withTrashed()
                ->whereNotNull('codigo')
                ->orderByRaw('CAST(codigo AS BIGINT) DESC')
                ->value('codigo');

            $proximo = $maior ? ((int) $maior + 1) : 100000001;

            // Garante 9 dígitos, começando em 100000001
            $codigo = (string) $proximo;

        } while (static::withTrashed()->where('codigo', $codigo)->exists());

        return $codigo;
    }
}
