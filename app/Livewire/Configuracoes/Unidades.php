<?php

namespace App\Livewire\Configuracoes;

use App\Models\Unidade;
use Livewire\Component;

class Unidades extends Component
{
    public string $busca = '';

    public ?int   $editandoId  = null;
    public string $nome        = '';
    public string $tipo        = 'filial';
    public string $cidade      = '';
    public string $uf          = '';
    public string $endereco    = '';
    public bool   $ativo       = true;
    public bool   $modalAberto = false;

    public function abrirCadastro(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    public function editar(int $id): void
    {
        $u = Unidade::findOrFail($id);
        $this->editandoId = $u->id;
        $this->nome       = $u->nome;
        $this->tipo       = $u->tipo ?? 'filial';
        $this->cidade     = $u->cidade ?? '';
        $this->uf         = $u->uf ?? '';
        $this->endereco   = $u->endereco ?? '';
        $this->ativo      = (bool) $u->ativo;
        $this->modalAberto = true;
    }

    public function salvar(): void
    {
        $this->validate([
            'nome'     => 'required|string|max:150',
            'cidade'   => 'nullable|string|max:100',
            'endereco' => 'nullable|string|max:255',
        ]);

        $dados = [
            'nome'     => $this->nome,
            'tipo'     => $this->tipo,
            'cidade'   => $this->cidade ?: null,
            'uf'       => $this->uf ?: null,
            'endereco' => $this->endereco ?: null,
            'ativo'    => $this->ativo,
        ];

        try {
            if ($this->editandoId) {
                Unidade::findOrFail($this->editandoId)->update($dados);
                $msg = 'Unidade atualizada com sucesso!';
            } else {
                Unidade::create($dados);
                $msg = 'Unidade criada com sucesso!';
            }

            $this->modalAberto = false;
            $this->resetForm();
            $this->dispatch('modal-sucesso', message: $msg);
        } catch (\Throwable) {
            $this->dispatch('modal-erro', message: 'Erro ao salvar unidade. Tente novamente.');
        }
    }

    public function toggleAtivo(int $id): void
    {
        $u = Unidade::findOrFail($id);
        $u->update(['ativo' => ! $u->ativo]);
    }

    public function excluir(int $id): void
    {
        Unidade::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Unidade removida.');
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editandoId = null;
        $this->nome       = '';
        $this->tipo       = 'filial';
        $this->cidade     = '';
        $this->uf         = '';
        $this->endereco   = '';
        $this->ativo      = true;
        $this->resetValidation();
    }

    public function render()
    {
        $unidades = Unidade::query()
            ->when($this->busca, fn($q) => $q->where('nome', 'like', "%{$this->busca}%")
                ->orWhere('cidade', 'like', "%{$this->busca}%"))
            ->orderBy('nome')
            ->get();

        return view('livewire.configuracoes.unidades', compact('unidades'))
            ->layout('layouts.app', ['title' => 'Unidades']);
    }
}
