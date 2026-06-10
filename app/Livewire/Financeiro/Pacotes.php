<?php

namespace App\Livewire\Financeiro;

use App\Models\Pacote;
use App\Models\PacoteItem;
use App\Models\TabelaPreco;
use App\Models\Unidade;
use Livewire\Component;

class Pacotes extends Component
{
    // Lista
    public string $busca = '';

    // Form
    public ?int    $editandoId    = null;
    public string  $nome          = '';
    public string  $descricao     = '';
    public string  $tipo_desconto = 'percentual';
    public string  $desconto      = '0';
    public string  $validade_dias = '';
    public ?int    $unidade_id    = null;
    public bool    $ativo         = true;
    public bool    $modalAberto   = false;

    // Itens do pacote (array de linhas editáveis)
    public array $itens = [];

    // Calculados
    public float $valorBruto  = 0;
    public float $valorFinal  = 0;
    public float $economiaVal = 0;

    // ── Abrir / fechar ──────────────────────────────────────────────

    public function abrirCadastro(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    public function editar(int $id): void
    {
        $pacote = Pacote::with('itens.tabelaPreco')->findOrFail($id);

        $this->editandoId    = $pacote->id;
        $this->nome          = $pacote->nome;
        $this->descricao     = $pacote->descricao ?? '';
        $this->tipo_desconto = $pacote->tipo_desconto;
        $this->desconto      = number_format((float) $pacote->desconto, 2, ',', '.');
        $this->validade_dias = (string) ($pacote->validade_dias ?? '');
        $this->unidade_id    = $pacote->unidade_id;
        $this->ativo         = (bool) $pacote->ativo;

        $this->itens = $pacote->itens->map(fn($item) => [
            'tabela_preco_id' => $item->tabela_preco_id,
            'nome'            => $item->tabelaPreco->nome ?? '',
            'tipo'            => $item->tabelaPreco->tipo ?? '',
            'quantidade'      => $item->quantidade,
            'valor_unitario'  => (float) $item->valor_unitario,
            'subtotal'        => (float) $item->subtotal,
        ])->toArray();

        $this->recalcular();
        $this->modalAberto = true;
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    // ── Itens ────────────────────────────────────────────────────────

    public function adicionarItem(int $tabelaPrecoId): void
    {
        $preco = TabelaPreco::find($tabelaPrecoId);
        if (! $preco) return;

        // Se já existe, incrementa quantidade
        foreach ($this->itens as &$item) {
            if ($item['tabela_preco_id'] === $tabelaPrecoId) {
                $item['quantidade']++;
                $item['subtotal'] = $item['quantidade'] * $item['valor_unitario'];
                $this->recalcular();
                return;
            }
        }

        $valor = (float) $preco->valor;

        $this->itens[] = [
            'tabela_preco_id' => $preco->id,
            'nome'            => $preco->nome,
            'tipo'            => $preco->tipo,
            'quantidade'      => 1,
            'valor_unitario'  => $valor,
            'subtotal'        => $valor,
        ];

        $this->recalcular();
    }

    public function removerItem(int $index): void
    {
        array_splice($this->itens, $index, 1);
        $this->recalcular();
    }

    public function updatedItens(): void
    {
        // Recalcula subtotais ao editar quantidade diretamente
        foreach ($this->itens as &$item) {
            $qtd = max(1, (int) ($item['quantidade'] ?? 1));
            $item['quantidade'] = $qtd;
            $item['subtotal']   = $qtd * ($item['valor_unitario'] ?? 0);
        }
        $this->recalcular();
    }

    public function incrementarQtd(int $index): void
    {
        if (! isset($this->itens[$index])) return;
        $this->itens[$index]['quantidade']++;
        $this->itens[$index]['subtotal'] = $this->itens[$index]['quantidade'] * $this->itens[$index]['valor_unitario'];
        $this->recalcular();
    }

    public function decrementarQtd(int $index): void
    {
        if (! isset($this->itens[$index])) return;
        if ($this->itens[$index]['quantidade'] <= 1) {
            $this->removerItem($index);
            return;
        }
        $this->itens[$index]['quantidade']--;
        $this->itens[$index]['subtotal'] = $this->itens[$index]['quantidade'] * $this->itens[$index]['valor_unitario'];
        $this->recalcular();
    }

    public function updatedDesconto(): void   { $this->recalcular(); }
    public function updatedTipoDesconto(): void { $this->recalcular(); }

    private function recalcular(): void
    {
        $this->valorBruto = array_sum(array_column($this->itens, 'subtotal'));

        $descontoNum = (float) str_replace(['.', ','], ['', '.'], $this->desconto);
        $this->valorFinal  = Pacote::calcularFinal($this->valorBruto, $this->tipo_desconto, $descontoNum);
        $this->economiaVal = $this->valorBruto - $this->valorFinal;
    }

    // ── Salvar ───────────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'nome'          => 'required|string|max:150',
            'tipo_desconto' => 'required|in:percentual,fixo',
            'desconto'      => 'required',
            'validade_dias' => 'nullable|integer|min:1|max:3650',
            'unidade_id'    => 'nullable|exists:unidades,id',
        ]);

        if (empty($this->itens)) {
            $this->dispatch('modal-erro', message: 'Adicione pelo menos um item ao pacote.');
            return;
        }

        $descontoNum = (float) str_replace(['.', ','], ['', '.'], $this->desconto);
        $bruto  = array_sum(array_column($this->itens, 'subtotal'));
        $final  = Pacote::calcularFinal($bruto, $this->tipo_desconto, $descontoNum);

        $dados = [
            'nome'          => $this->nome,
            'descricao'     => $this->descricao ?: null,
            'tipo_desconto' => $this->tipo_desconto,
            'desconto'      => $descontoNum,
            'valor_bruto'   => $bruto,
            'valor_final'   => $final,
            'validade_dias' => $this->validade_dias !== '' ? (int) $this->validade_dias : null,
            'unidade_id'    => $this->unidade_id,
            'ativo'         => $this->ativo,
        ];

        try {
            if ($this->editandoId) {
                $pacote = Pacote::findOrFail($this->editandoId);
                $pacote->update($dados);
                $pacote->itens()->delete();
                $msg = 'Pacote atualizado com sucesso!';
            } else {
                $pacote = Pacote::create($dados);
                $msg = 'Pacote criado com sucesso!';
            }

            foreach ($this->itens as $item) {
                PacoteItem::create([
                    'pacote_id'       => $pacote->id,
                    'tabela_preco_id' => $item['tabela_preco_id'],
                    'quantidade'      => $item['quantidade'],
                    'valor_unitario'  => $item['valor_unitario'],
                    'subtotal'        => $item['subtotal'],
                ]);
            }

            $this->modalAberto = false;
            $this->resetForm();
            $this->dispatch('modal-sucesso', message: $msg);
        } catch (\Throwable $e) {
            $this->dispatch('modal-erro', message: 'Erro ao salvar o pacote. Tente novamente.');
        }
    }

    public function toggleAtivo(int $id): void
    {
        $pacote = Pacote::findOrFail($id);
        $pacote->update(['ativo' => ! $pacote->ativo]);
    }

    public function excluir(int $id): void
    {
        try {
            Pacote::findOrFail($id)->delete();
            $this->dispatch('modal-sucesso', message: 'Pacote removido com sucesso!');
        } catch (\Throwable) {
            $this->dispatch('modal-erro', message: 'Erro ao remover o pacote.');
        }
    }

    private function resetForm(): void
    {
        $this->editandoId    = null;
        $this->nome          = '';
        $this->descricao     = '';
        $this->tipo_desconto = 'percentual';
        $this->desconto      = '0';
        $this->validade_dias = '';
        $this->unidade_id    = null;
        $this->ativo         = true;
        $this->itens         = [];
        $this->valorBruto    = 0;
        $this->valorFinal    = 0;
        $this->economiaVal   = 0;
        $this->resetValidation();
    }

    public function render()
    {
        $pacotes = Pacote::query()
            ->withCount('itens')
            ->when($this->busca, fn($q) => $q->where('nome', 'like', "%{$this->busca}%"))
            ->orderBy('nome')
            ->get();

        $tabelaPrecos = TabelaPreco::ativos()
            ->orderBy('tipo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'tipo', 'valor']);

        $unidades = Unidade::ativas()->orderBy('nome')->get(['id', 'nome']);

        return view('livewire.financeiro.pacotes', compact('pacotes', 'tabelaPrecos', 'unidades'))
            ->layout('layouts.app', ['title' => 'Pacotes']);
    }
}
