<?php

namespace App\Livewire\Configuracoes;

use App\Models\Sala;
use App\Models\Unidade;
use App\Traits\UsaFilialAtiva;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Salas extends Component
{
    use WithPagination, UsaFilialAtiva;
    // ── Filtros ──────────────────────────────────────────────────────────────
    public string $busca        = '';
    public string $filtroUnidade = '';
    public string $filtroStatus  = '';
    public string $filtroTipo       = '';
    public string $filtroCapacidade = ''; // '1','2-4','5-9','10+'
    public int    $porPagina        = 15;
    public string $visualizacao  = 'tabela';
    public array  $selecionados   = [];
    public bool   $todosRegistros = false;

    // ── Form ─────────────────────────────────────────────────────────────────
    public ?int   $editandoId  = null;
    public bool   $modalAberto = false;

    public string $nome       = '';
    public string $tipo       = '';
    public string $cor        = '#3b82f6';
    public int    $capacidade = 1;
    public string $descricao  = '';
    public bool   $ativo      = true;
    public string $unidade_id = '';

    // ── Auxiliares ───────────────────────────────────────────────────────────
    public array $unidades = [];

    public static array $tipos = [
        'consultorio'  => 'Consultório',
        'procedimento' => 'Sala de Procedimento',
        'cirurgia'     => 'Centro Cirúrgico',
        'exame'        => 'Sala de Exame',
        'espera'       => 'Sala de Espera',
        'recepcao'     => 'Recepção',
        'reuniao'      => 'Sala de Reunião',
        'outro'        => 'Outro',
    ];

    public static array $cores = [
        '#3b82f6', '#22c55e', '#f59e0b', '#ec4899',
        '#8b5cf6', '#0d9488', '#ef4444', '#f97316',
        '#06b6d4', '#84cc16', '#6366f1', '#14b8a6',
    ];

    public function mount(): void
    {
        $this->unidades = Unidade::ativas()->orderBy('nome')->get(['id', 'nome'])->toArray();
    }

    public function updatingBusca(): void         { $this->resetPage(); }
    public function updatingFiltroUnidade(): void  { $this->resetPage(); }
    public function updatingFiltroStatus(): void   { $this->resetPage(); }
    public function updatingFiltroTipo(): void        { $this->resetPage(); }
    public function updatingFiltroCapacidade(): void  { $this->resetPage(); }

    public function limparFiltros(): void
    {
        $this->busca              = '';
        $this->filtroStatus       = '';
        $this->filtroTipo         = '';
        $this->filtroUnidade      = '';
        $this->filtroCapacidade   = '';
        $this->resetPage();
    }

    // ── Modal ────────────────────────────────────────────────────────────────

    public function abrirCadastro(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    public function editar(int $id): void
    {
        $s = Sala::findOrFail($id);
        $this->editandoId  = $s->id;
        $this->nome        = $s->nome;
        $this->tipo        = $s->tipo ?? '';
        $this->cor         = $s->cor ?? '#3b82f6';
        $this->capacidade  = $s->capacidade ?? 1;
        $this->descricao   = $s->descricao ?? '';
        $this->ativo       = (bool) $s->ativo;
        $this->unidade_id  = $s->unidade_id ? (string) $s->unidade_id : '';
        $this->modalAberto = true;
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'nome'       => 'required|string|max:150',
            'capacidade' => 'required|integer|min:1',
            'cor'        => 'required|string|max:7',
        ]);

        $dados = [
            'nome'        => $this->nome,
            'tipo'        => $this->tipo ?: null,
            'cor'         => $this->cor,
            'capacidade'  => $this->capacidade,
            'descricao'   => $this->descricao ?: null,
            'ativo'       => $this->ativo,
            'unidade_id'  => $this->unidade_id ? (int) $this->unidade_id : null,
        ];

        try {
            if ($this->editandoId) {
                Sala::findOrFail($this->editandoId)->update($dados);
                $msg = 'Sala atualizada com sucesso!';
            } else {
                Sala::create($dados);
                $msg = 'Sala cadastrada com sucesso!';
            }

            $this->modalAberto = false;
            $this->resetForm();
            $this->dispatch('modal-sucesso', message: $msg);
        } catch (\Throwable $e) {
            $this->dispatch('modal-erro', message: 'Erro ao salvar sala. Tente novamente.');
        }
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $salas = $this->queryBase()->get();

        return response()->streamDownload(function () use ($salas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Nome', 'Tipo', 'Unidade', 'Capacidade', 'Status'], ';');
            foreach ($salas as $s) {
                fputcsv($out, [
                    $s->nome,
                    self::$tipos[$s->tipo] ?? $s->tipo ?? '',
                    $s->unidade?->nome ?? '',
                    $s->capacidade,
                    $s->ativo ? 'Ativa' : 'Inativa',
                ], ';');
            }
            fclose($out);
        }, 'salas_' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function toggleSelecionado(int $id): void
    {
        $this->todosRegistros = false;
        if (in_array($id, $this->selecionados)) {
            $this->selecionados = array_values(array_filter($this->selecionados, fn($i) => $i !== $id));
        } else {
            $this->selecionados[] = $id;
        }
    }

    public function selecionarTodosVisiveis(array $ids): void
    {
        $this->todosRegistros = false;
        $todos = count(array_intersect($ids, $this->selecionados)) === count($ids);
        if ($todos) {
            $this->selecionados = array_values(array_filter($this->selecionados, fn($i) => !in_array($i, $ids)));
        } else {
            $this->selecionados = array_values(array_unique(array_merge($this->selecionados, $ids)));
        }
    }

    public function selecionarTodos(): void
    {
        $this->selecionados   = $this->queryBase()->pluck('id')->toArray();
        $this->todosRegistros = true;
    }

    public function limparSelecao(): void
    {
        $this->selecionados   = [];
        $this->todosRegistros = false;
    }

    public function desativarSelecionadas(): void
    {
        if (empty($this->selecionados)) return;
        $count = count($this->selecionados);
        Sala::whereIn('id', $this->selecionados)->update(['ativo' => false]);
        $this->selecionados = [];
        $this->dispatch('modal-success', title: 'Concluído!', message: "{$count} sala(s) desativada(s) com sucesso.");
    }

    public function ativarSelecionadas(): void
    {
        if (empty($this->selecionados)) return;
        $count = count($this->selecionados);
        Sala::whereIn('id', $this->selecionados)->update(['ativo' => true]);
        $this->selecionados = [];
        $this->dispatch('modal-success', title: 'Concluído!', message: "{$count} sala(s) ativada(s) com sucesso.");
    }

    #[Computed]
    public function selecionadasTodasInativas(): bool
    {
        if (empty($this->selecionados)) return false;
        return !Sala::whereIn('id', $this->selecionados)->where('ativo', true)->exists();
    }

    public function toggleAtivo(int $id): void
    {
        $sala = Sala::findOrFail($id);
        $sala->update(['ativo' => !$sala->ativo]);
        $status = $sala->ativo ? 'ativada' : 'desativada';
        $this->dispatch('modal-success', title: 'Concluído!', message: "Sala {$status} com sucesso.");
    }

    public function excluir(int $id): void
    {
        Sala::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Excluída!', message: 'Sala removida com sucesso.');
    }

    public function exportarSelecionadas(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $salas = Sala::with('unidade')->whereIn('id', $this->selecionados)->orderBy('nome')->get();
        return response()->streamDownload(function () use ($salas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Nome', 'Tipo', 'Unidade', 'Capacidade', 'Status'], ';');
            foreach ($salas as $s) {
                fputcsv($out, [
                    $s->nome,
                    self::$tipos[$s->tipo] ?? $s->tipo ?? '',
                    $s->unidade?->nome ?? '',
                    $s->capacidade,
                    $s->ativo ? 'Ativa' : 'Inativa',
                ], ';');
            }
            fclose($out);
        }, 'salas-selecionadas-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }


    private function resetForm(): void
    {
        $this->editandoId = null;
        $this->nome       = '';
        $this->tipo       = '';
        $this->cor        = '#3b82f6';
        $this->capacidade = 1;
        $this->descricao  = '';
        $this->ativo      = true;
        $this->unidade_id = '';
        $this->resetValidation();
    }

    // ── Query base ───────────────────────────────────────────────────────────

    private function queryBase()
    {
        return Sala::with('unidade')
            ->when($this->temFilial() && ! $this->filtroUnidade,
                fn($q) => $this->scopeFilialDireta($q)
            )
            ->when($this->busca, fn($q) => $q->where('nome', 'like', "%{$this->busca}%")
                ->orWhere('tipo', 'like', "%{$this->busca}%"))
            ->when($this->filtroUnidade, fn($q) => $q->where('unidade_id', (int) $this->filtroUnidade))
            ->when($this->filtroTipo,    fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroStatus === 'ativo',   fn($q) => $q->where('ativo', true))
            ->when($this->filtroStatus === 'inativo', fn($q) => $q->where('ativo', false))
            ->when($this->filtroCapacidade === '1',    fn($q) => $q->where('capacidade', 1))
            ->when($this->filtroCapacidade === '2-4',  fn($q) => $q->whereBetween('capacidade', [2, 4]))
            ->when($this->filtroCapacidade === '5-9',  fn($q) => $q->whereBetween('capacidade', [5, 9]))
            ->when($this->filtroCapacidade === '10+',  fn($q) => $q->where('capacidade', '>=', 10))
            ->orderBy('nome');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $salas         = $this->queryBase()->paginate($this->porPagina);
        $totalFiltrado = $this->queryBase()->count();

        $baseStat = Sala::query()
            ->when($this->temFilial(), fn($q) => $this->scopeFilialDireta($q));

        $stats = [
            'total'   => (clone $baseStat)->count(),
            'ativas'  => (clone $baseStat)->where('ativo', true)->count(),
            'inativas'=> (clone $baseStat)->where('ativo', false)->count(),
        ];

        return view('livewire.configuracoes.salas', compact('salas', 'stats', 'totalFiltrado'))
            ->layout('layouts.app', ['title' => 'Salas']);
    }
}
