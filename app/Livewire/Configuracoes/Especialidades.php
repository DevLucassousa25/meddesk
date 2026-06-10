<?php

namespace App\Livewire\Configuracoes;

use App\Models\Especialidade;
use App\Models\Unidade;
use Livewire\Component;

class Especialidades extends Component
{
    // Lista
    public string $busca = '';

    // Form
    public ?int   $editandoId            = null;
    public string $nome                  = '';
    public string $observacoes           = '';
    public array  $unidades_selecionadas = [];
    public bool   $ativo                 = true;
    public bool   $modalAberto           = false;

    // Confirmação exclusão
    public ?int $excluindoId = null;

    public function abrirCadastro(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    public function editar(int $id): void
    {
        $esp = Especialidade::findOrFail($id);
        $this->editandoId            = $esp->id;
        $this->nome                  = $esp->nome;
        $this->unidades_selecionadas = array_map('intval', $esp->unidade_ids ?? []);
        $this->observacoes           = $esp->observacoes ?? '';
        $this->ativo                 = (bool) $esp->ativo;
        $this->modalAberto           = true;
    }

    // ── Seleção de unidades ─────────────────────────────────────────

    public function toggleUnidade(int $id): void
    {
        if (in_array($id, $this->unidades_selecionadas)) {
            $this->unidades_selecionadas = array_values(
                array_filter($this->unidades_selecionadas, fn($v) => $v !== $id)
            );
        } else {
            $this->unidades_selecionadas[] = $id;
        }
    }

    public function selecionarTodasUnidades(): void
    {
        $this->unidades_selecionadas = Unidade::ativas()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();
    }

    public function limparSelecaoUnidades(): void
    {
        $this->unidades_selecionadas = [];
    }

    // ── Salvar ───────────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'nome'        => 'required|string|max:150',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $dados = [
            'nome'        => $this->nome,
            'observacoes' => $this->observacoes ?: null,
            'unidade_ids' => !empty($this->unidades_selecionadas)
                                ? array_values($this->unidades_selecionadas)
                                : null,
            'ativo'       => $this->ativo,
        ];

        try {
            if ($this->editandoId) {
                Especialidade::findOrFail($this->editandoId)->update($dados);
                $msg = 'Especialidade atualizada com sucesso!';
            } else {
                Especialidade::create($dados);
                $msg = 'Especialidade criada com sucesso!';
            }

            $this->modalAberto = false;
            $this->resetForm();
            $this->dispatch('modal-sucesso', message: $msg);
        } catch (\Throwable $e) {
            $this->dispatch('modal-erro', message: $e->getMessage());
        }
    }

    public function toggleAtivo(int $id): void
    {
        $esp = Especialidade::findOrFail($id);
        $esp->update(['ativo' => ! $esp->ativo]);
    }

    public function excluir(int $id): void
    {
        try {
            Especialidade::findOrFail($id)->delete();
            $this->dispatch('modal-sucesso', message: 'Especialidade removida com sucesso!');
        } catch (\Throwable) {
            $this->dispatch('modal-erro', message: 'Erro ao remover especialidade.');
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editandoId            = null;
        $this->nome                  = '';
        $this->observacoes           = '';
        $this->unidades_selecionadas = [];
        $this->ativo                 = true;
        $this->resetValidation();
    }

    public function render()
    {
        $especialidades = Especialidade::query()
            ->when($this->busca, fn($q) => $q->where('nome', 'like', "%{$this->busca}%"))
            ->orderBy('nome')
            ->get();

        $unidades = Unidade::ativas()->orderBy('nome')->get(['id', 'nome']);

        return view('livewire.configuracoes.especialidades', compact('especialidades', 'unidades'))
            ->layout('layouts.app', ['title' => 'Especialidades']);
    }
}
