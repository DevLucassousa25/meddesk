<?php

namespace App\Traits;

use App\Models\Unidade;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait para componentes Livewire aplicarem o filtro de filial ativa.
 *
 * Uso em qualquer componente:
 *   use UsaFilialAtiva;
 *
 * Na query:
 *   ->when($this->filialId(), fn($q) => $q->where('unidade_id', $this->filialId()))
 *   ->tap(fn($q) => $this->scopeFilialProfissional($q))
 */
trait UsaFilialAtiva
{
    // ── Acesso básico ────────────────────────────────────────────────────────

    /** ID da filial ativa na sessão (null = todas). */
    public function filialId(): ?int
    {
        return session('unidade_ativa_id');
    }

    /** Model Unidade da filial ativa, ou null. */
    public function filialAtiva(): ?Unidade
    {
        $id = $this->filialId();
        return $id ? Unidade::find($id) : null;
    }

    /** True se uma filial específica está selecionada. */
    public function temFilial(): bool
    {
        return (bool) session('unidade_ativa_id');
    }

    // ── Escopos prontos para uso nas queries ─────────────────────────────────

    /**
     * Filtra modelos com coluna unidade_id direta (ex: Sala).
     * ->when($this->temFilial(), fn($q) => $this->scopeFilialDireta($q))
     */
    public function scopeFilialDireta(Builder $query): Builder
    {
        return $query->where('unidade_id', $this->filialId());
    }

    /**
     * Filtra Profissionais pela relação hasMany ProfissionalUnidade (usa nome da unidade).
     * ->when($this->temFilial(), fn($q) => $this->scopeFilialProfissional($q))
     */
    public function scopeFilialProfissional(Builder $query): Builder
    {
        $nome = $this->filialAtiva()?->nome;
        if (! $nome) return $query;
        return $query->whereHas('unidades', fn($u) => $u->where('unidade', $nome));
    }

    /**
     * Filtra Pacientes pelo profissional responsável que atende na filial.
     * ->when($this->temFilial(), fn($q) => $this->scopeFilialPaciente($q))
     */
    public function scopeFilialPaciente(Builder $query): Builder
    {
        $nome = $this->filialAtiva()?->nome;
        if (! $nome) return $query;
        return $query->whereHas('profissionalResponsavel.unidades', fn($u) => $u->where('unidade', $nome));
    }
}
