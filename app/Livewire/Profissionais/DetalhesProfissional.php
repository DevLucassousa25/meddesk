<?php

namespace App\Livewire\Profissionais;

use App\Models\Atendimento;
use App\Models\Profissional;
use Livewire\Component;
use Livewire\WithPagination;

class DetalhesProfissional extends Component
{
    use WithPagination;

    public Profissional $profissional;
    public string $tab = 'geral';

    public function mount(int $profissionalId): void
    {
        $this->profissional = Profissional::with([
            'especialidades',
            'unidades',
            'pacientes',
        ])->findOrFail($profissionalId);
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $p = $this->profissional;

        $atendimentos = Atendimento::where('profissional_id', $p->id)
            ->with('paciente')
            ->latest('data_atendimento')
            ->paginate(15);

        $pacientes = $p->pacientes()
            ->orderBy('nome')
            ->paginate(15);

        return view('livewire.profissionais.detalhes-profissional', [
            'profissional' => $p,
            'atendimentos' => $atendimentos,
            'pacientes'    => $pacientes,
        ])->layout('layouts.app', ['title' => $p->nome]);
    }
}
