<?php

namespace App\Livewire\Configuracoes;

use App\Models\Especialidade;
use App\Models\TabelaPreco;
use App\Models\Unidade;
use Livewire\Component;

class TabelaPrecos extends Component
{
    // Filtros
    public string $busca      = '';
    public string $filtroTipo = '';

    // Form
    public ?int   $editandoId           = null;
    public string $tipo                 = 'consulta';
    public string $nome                 = '';
    public string $descricao            = '';
    public string $valor                = '';
    public string $codigo               = '';
    public ?int   $especialidade_id     = null;
    public array  $unidades_selecionadas = [];   // [] = todas as unidades
    public string $duracao_minutos      = '';
    public bool   $ativo                = true;
    public bool   $modalAberto          = false;

    // ── Abrir / fechar ──────────────────────────────────────────────

    public function abrirCadastro(): void
    {
        $this->resetForm();
        $this->codigo      = $this->gerarCodigo($this->tipo);
        $this->modalAberto = true;
    }

    public function editar(int $id): void
    {
        $item = TabelaPreco::findOrFail($id);

        $this->editandoId            = $item->id;
        $this->tipo                  = $item->tipo;
        $this->nome                  = $item->nome;
        $this->descricao             = $item->descricao ?? '';
        $this->valor                 = number_format((float) $item->valor, 2, ',', '.');
        $this->codigo                = $item->codigo ?? '';
        $this->especialidade_id      = $item->especialidade_id;
        $this->unidades_selecionadas = array_map('intval', $item->unidade_ids ?? []);
        $this->duracao_minutos       = (string) ($item->duracao_minutos ?? '');
        $this->ativo                 = (bool) $item->ativo;
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
            'tipo'            => 'required|in:consulta,exame,atendimento',
            'nome'            => 'required|string|max:150',
            'valor'           => 'required',
            'descricao'       => 'nullable|string|max:1000',
            'codigo'          => 'nullable|string|max:50',
            'especialidade_id'=> 'nullable|exists:especialidades,id',
            'duracao_minutos' => 'nullable|integer|min:1|max:1440',
        ]);

        $valorNumerico = (float) str_replace(['.', ','], ['', '.'], $this->valor);

        $dados = [
            'tipo'             => $this->tipo,
            'nome'             => $this->nome,
            'descricao'        => $this->descricao ?: null,
            'valor'            => $valorNumerico,
            'codigo'           => $this->codigo ?: null,
            'especialidade_id' => $this->especialidade_id,
            'unidade_ids'      => !empty($this->unidades_selecionadas)
                                    ? array_values($this->unidades_selecionadas)
                                    : null,
            'duracao_minutos'  => $this->duracao_minutos !== '' ? (int) $this->duracao_minutos : null,
            'ativo'            => $this->ativo,
        ];

        try {
            if ($this->editandoId) {
                TabelaPreco::findOrFail($this->editandoId)->update($dados);
                $msg = 'Item atualizado com sucesso!';
            } else {
                TabelaPreco::create($dados);
                $msg = 'Item cadastrado com sucesso!';
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
        $item = TabelaPreco::findOrFail($id);
        $item->update(['ativo' => ! $item->ativo]);
    }

    public function excluir(int $id): void
    {
        try {
            TabelaPreco::findOrFail($id)->delete();
            $this->dispatch('modal-sucesso', message: 'Item removido com sucesso!');
        } catch (\Throwable) {
            $this->dispatch('modal-erro', message: 'Erro ao remover o item.');
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function updatedTipo(): void
    {
        if (! $this->editandoId) {
            $this->codigo = $this->gerarCodigo($this->tipo);
        }
    }

    private function gerarCodigo(string $tipo): string
    {
        $prefixo = match ($tipo) {
            'consulta'    => 'CON',
            'exame'       => 'EXA',
            'atendimento' => 'ATE',
            default       => 'ITM',
        };

        $ultimo = TabelaPreco::where('tipo', $tipo)
            ->where('codigo', 'like', "{$prefixo}-%")
            ->orderByDesc('id')
            ->value('codigo');

        $proximo = 1;
        if ($ultimo && preg_match('/-(\d+)$/', $ultimo, $m)) {
            $proximo = (int) $m[1] + 1;
        }

        return $prefixo . '-' . str_pad($proximo, 4, '0', STR_PAD_LEFT);
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editandoId            = null;
        $this->tipo                  = 'consulta';
        $this->nome                  = '';
        $this->descricao             = '';
        $this->valor                 = '';
        $this->codigo                = '';
        $this->especialidade_id      = null;
        $this->unidades_selecionadas = [];
        $this->duracao_minutos       = '';
        $this->ativo                 = true;
        $this->resetValidation();
    }

    public function render()
    {
        $itens = TabelaPreco::query()
            ->with('especialidade')
            ->when($this->busca, fn($q) => $q->where('nome', 'like', "%{$this->busca}%")
                ->orWhere('codigo', 'like', "%{$this->busca}%"))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get();

        $especialidades = Especialidade::ativas()->orderBy('nome')->get(['id', 'nome']);
        $unidades       = Unidade::ativas()->orderBy('nome')->get(['id', 'nome']);

        $totais = [
            'consulta'    => TabelaPreco::doTipo('consulta')->count(),
            'exame'       => TabelaPreco::doTipo('exame')->count(),
            'atendimento' => TabelaPreco::doTipo('atendimento')->count(),
        ];

        return view('livewire.configuracoes.tabela-precos', compact('itens', 'especialidades', 'unidades', 'totais'))
            ->layout('layouts.app', ['title' => 'Tabela de Preços']);
    }
}
