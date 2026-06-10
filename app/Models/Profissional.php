<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profissional extends Model
{
    protected $table = 'profissionais';

    protected $fillable = [
        'nome', 'identificacao', 'senha_celular', 'cpf', 'rg', 'conselho_profissional',
        'cep', 'endereco', 'numero', 'bairro', 'cidade', 'uf',
        'email', 'telefone', 'celular1', 'celular2', 'data_nascimento',
        'especialidade', 'crm', 'cro',
        'ordem_agenda', 'pode_estender_horarios', 'horarios_flexiveis', 'receber_lembrete_evoluir',
        'comissao_personalizada', 'percentual_comissao',
        'perm_ver_somente_seus', 'perm_fluxo_caixa', 'perm_agendar_celular', 'perm_editar_agenda',
        'perm_alterar_status', 'perm_acessar_cadastro', 'perm_editar_recebimentos', 'perm_remover_recebimentos',
        'foto', 'ativo',
    ];

    protected $casts = [
        'ativo'                    => 'boolean',
        'data_nascimento'          => 'date',
        'pode_estender_horarios'   => 'boolean',
        'horarios_flexiveis'       => 'boolean',
        'receber_lembrete_evoluir' => 'boolean',
        'comissao_personalizada'   => 'boolean',
        'perm_ver_somente_seus'    => 'boolean',
        'perm_fluxo_caixa'         => 'boolean',
        'perm_agendar_celular'     => 'boolean',
        'perm_editar_agenda'       => 'boolean',
        'perm_alterar_status'      => 'boolean',
        'perm_acessar_cadastro'    => 'boolean',
        'perm_editar_recebimentos' => 'boolean',
        'perm_remover_recebimentos'=> 'boolean',
    ];

    public function pacientes(): HasMany
    {
        return $this->hasMany(Paciente::class, 'profissional_responsavel_id');
    }

    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class, 'profissional_id');
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'profissional_id');
    }

    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(Especialidade::class, 'profissional_especialidade');
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(ProfissionalUnidade::class);
    }

    public static function gerarCodigo(): string
    {
        do {
            // PostgreSQL: ~ para regex, BIGINT para cast numérico
            $maior = static::whereNotNull('identificacao')
                ->whereRaw("identificacao ~ '^[0-9]+$'")
                ->orderByRaw('CAST(identificacao AS BIGINT) DESC')
                ->value('identificacao');

            $proximo = $maior ? ((int) $maior + 1) : 1000;
            $codigo  = (string) $proximo;
        } while (static::where('identificacao', $codigo)->exists());

        return $codigo;
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
