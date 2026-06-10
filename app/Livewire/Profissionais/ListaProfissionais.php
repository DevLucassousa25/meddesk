<?php

namespace App\Livewire\Profissionais;

use App\Models\Especialidade;
use App\Models\Profissional;
use App\Models\Unidade;
use App\Traits\UsaFilialAtiva;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListaProfissionais extends Component
{
    use WithPagination, UsaFilialAtiva;

    public string $busca               = '';
    public string $filtroStatus        = '';
    public string $filtroEspecialidade = '';
    public string $filtroUnidade       = '';
    public bool   $filtroComConselho   = false;
    public int    $porPagina           = 15;
    public string $visualizacao        = 'tabela';
    public array  $selecionados        = [];
    public bool   $todosRegistros      = false; // true = selecionou todos os registros (não só página)

    // Auxiliares para selects
    public array $especialidades = [];
    public array $unidades       = [];

    public function mount(): void
    {
        $this->especialidades = Especialidade::where('ativo', true)->orderBy('nome')->get(['id','nome'])->toArray();
        $this->unidades       = Unidade::where('ativo', true)->orderBy('nome')->get(['id','nome'])->toArray();
    }

    public function updatingBusca(): void              { $this->resetPage(); }
    public function updatingFiltroStatus(): void       { $this->resetPage(); }
    public function updatingFiltroEspecialidade(): void { $this->resetPage(); }
    public function updatingFiltroUnidade(): void      { $this->resetPage(); }
    public function updatingFiltroComConselho(): void  { $this->resetPage(); }

    public function limparFiltros(): void
    {
        $this->busca               = '';
        $this->filtroStatus        = '';
        $this->filtroEspecialidade = '';
        $this->filtroUnidade       = '';
        $this->filtroComConselho   = false;
        $this->resetPage();
    }

    public function temFiltrosAtivos(): bool
    {
        return $this->busca !== ''
            || $this->filtroStatus !== ''
            || $this->filtroEspecialidade !== ''
            || $this->filtroUnidade !== ''
            || $this->filtroComConselho;
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
        $todosJaSelecionados  = count(array_intersect($ids, $this->selecionados)) === count($ids);
        if ($todosJaSelecionados) {
            $this->selecionados = array_values(array_filter($this->selecionados, fn($i) => !in_array($i, $ids)));
        } else {
            $this->selecionados = array_values(array_unique(array_merge($this->selecionados, $ids)));
        }
    }

    public function selecionarTodos(): void
    {
        $this->selecionados  = $this->queryBase()->pluck('id')->toArray();
        $this->todosRegistros = true;
    }

    public function limparSelecao(): void
    {
        $this->selecionados   = [];
        $this->todosRegistros = false;
    }

    public function inativarSelecionados(?array $ids = null): void
    {
        $ids = $ids ?? $this->selecionados;
        if (empty($ids)) return;
        $count = count($ids);
        Profissional::whereIn('id', $ids)->update(['ativo' => false]);
        $this->selecionados = array_values(array_diff($this->selecionados, $ids));
        $this->dispatch('modal-success', title: 'Concluído!', message: "{$count} profissional(is) inativado(s) com sucesso.");
    }

    public function exportarSelecionados(): StreamedResponse
    {
        $profissionais = Profissional::whereIn('id', $this->selecionados)->orderBy('nome')->get();
        return response()->streamDownload(function () use ($profissionais) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Nome', 'Código', 'CPF', 'E-mail', 'Celular', 'Conselho', 'Status'], ';');
            foreach ($profissionais as $p) {
                fputcsv($out, [
                    $p->nome, $p->identificacao ?? '', $p->cpf ?? '',
                    $p->email ?? '', $p->celular1 ?: $p->telefone ?: '',
                    $p->conselho_profissional ?? '', $p->ativo ? 'Ativo' : 'Inativo',
                ], ';');
            }
            fclose($out);
        }, 'profissionais-selecionados-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function ativar(int $id): void
    {
        Profissional::findOrFail($id)->update(['ativo' => true]);
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Profissional ativado com sucesso.');
    }

    public function ativarSelecionados(): void
    {
        if (empty($this->selecionados)) return;
        $count = count($this->selecionados);
        Profissional::whereIn('id', $this->selecionados)->update(['ativo' => true]);
        $this->selecionados = [];
        $this->dispatch('modal-success', title: 'Concluído!', message: "{$count} profissional(is) ativado(s) com sucesso.");
    }

    #[Computed]
    public function selecionadosTodosInativos(): bool
    {
        if (empty($this->selecionados)) return false;
        return !Profissional::whereIn('id', $this->selecionados)->where('ativo', true)->exists();
    }

    public function excluir(int $id): void
    {
        Profissional::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Profissional removido.');
    }

    public function exportarCsv(): StreamedResponse
    {
        $profissionais = $this->queryBase()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="profissionais_' . now()->format('Ymd_His') . '.csv"',
        ];

        return response()->streamDownload(function () use ($profissionais) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($out, ['Nome', 'Código', 'CPF', 'E-mail', 'Celular', 'Conselho', 'Status'], ';');
            foreach ($profissionais as $p) {
                fputcsv($out, [
                    $p->nome,
                    $p->identificacao ?? '',
                    $p->cpf ?? '',
                    $p->email ?? '',
                    $p->celular1 ?: $p->telefone ?: '',
                    $p->conselho_profissional ?? '',
                    $p->ativo ? 'Ativo' : 'Inativo',
                ], ';');
            }
            fclose($out);
        }, 'profissionais.csv', $headers);
    }

    private function queryBase()
    {
        return Profissional::query()
            ->when($this->temFilial() && ! $this->filtroUnidade,
                fn($q) => $this->scopeFilialProfissional($q)
            )
            ->when($this->busca, fn($q) => $q->where(fn($q) =>
                $q->where('nome',   'like', "%{$this->busca}%")
                  ->orWhere('email','like', "%{$this->busca}%")
                  ->orWhere('cpf',  'like', "%{$this->busca}%")
                  ->orWhere('identificacao', 'like', "%{$this->busca}%")
            ))
            ->when($this->filtroStatus === 'ativo',   fn($q) => $q->where('ativo', true))
            ->when($this->filtroStatus === 'inativo', fn($q) => $q->where('ativo', false))
            ->when($this->filtroEspecialidade, fn($q) =>
                $q->whereHas('especialidades', fn($e) => $e->where('especialidades.id', (int) $this->filtroEspecialidade))
            )
            ->when($this->filtroUnidade, fn($q) =>
                $q->whereHas('unidades', fn($u) => $u->where('unidade', $this->filtroUnidade))
            )
            ->when($this->filtroComConselho, fn($q) =>
                $q->whereNotNull('conselho_profissional')->where('conselho_profissional', '!=', '')
            )
            ->with(['especialidades', 'unidades'])
            ->orderBy('nome');
    }

    public function render()
    {
        $base = Profissional::query()
            ->when($this->temFilial(), fn($q) => $this->scopeFilialProfissional($q));

        $stats = [
            'total'   => (clone $base)->count(),
            'ativos'  => (clone $base)->where('ativo', true)->count(),
            'inativos'=> (clone $base)->where('ativo', false)->count(),
        ];

        $profissionais = $this->queryBase()->paginate($this->porPagina);
        $totalFiltrado = $this->queryBase()->count();

        return view('livewire.profissionais.lista-profissionais', compact('profissionais', 'stats', 'totalFiltrado'))
            ->layout('layouts.app', ['title' => 'Profissionais']);
    }
}
