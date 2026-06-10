<?php

namespace App\Livewire\Clientes;

use App\Models\Agendamento;
use App\Models\AnotacaoPaciente;
use App\Models\Atendimento;
use App\Models\Cobranca;
use App\Models\DocumentoPaciente;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\ProntuarioEntrada;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DetalhesPaciente extends Component
{
    use WithFileUploads, WithPagination;

    // ── Paciente ──────────────────────────────────────────────────────────────
    public Paciente $paciente;
    public string $tab = 'geral';

    // ── Dados auxiliares ──────────────────────────────────────────────────────
    public array $profissionais = [];

    // ── Modal genérico ────────────────────────────────────────────────────────
    public bool   $modal       = false;
    public string $modalTipo   = ''; // atendimento | agendamento | documento | anotacao | cobranca | prontuario
    public ?int   $editandoId  = null;

    // ── Atendimento ───────────────────────────────────────────────────────────
    public string $atd_data          = '';
    public string $atd_hora          = '';
    public string $atd_tipo          = 'consulta';
    public string $atd_status        = 'realizado';
    public string $atd_profissional  = '';
    public string $atd_local         = '';
    public string $atd_observacoes   = '';

    // ── Agendamento ───────────────────────────────────────────────────────────
    public string $ag_data_hora      = '';
    public string $ag_tipo           = 'consulta';
    public string $ag_status         = 'agendado';
    public string $ag_duracao        = '30';
    public string $ag_profissional   = '';
    public string $ag_observacoes    = '';

    // ── Documento ─────────────────────────────────────────────────────────────
    public        $doc_arquivo       = null;
    public string $doc_nome          = '';
    public string $doc_tipo          = 'outros';
    public string $doc_descricao     = '';
    public string $doc_data          = '';

    // ── Anotação ──────────────────────────────────────────────────────────────
    public string $anot_titulo    = '';
    public string $anot_conteudo  = '';
    public string $anot_cor       = 'blue';
    public bool   $anot_fixada    = false;

    // ── Cobrança ──────────────────────────────────────────────────────────────
    public string $cob_descricao      = '';
    public string $cob_valor          = '';
    public string $cob_vencimento     = '';
    public string $cob_pagamento      = '';
    public string $cob_status         = 'pendente';
    public string $cob_forma          = '';
    public string $cob_observacoes    = '';

    // ── Prontuário ────────────────────────────────────────────────────────────
    public string $pron_data          = '';
    public string $pron_tipo          = 'evolucao';
    public string $pron_titulo        = '';
    public string $pron_conteudo      = '';
    public string $pron_profissional  = '';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $id): void
    {
        $this->paciente = Paciente::with([
            'convenio', 'tipoVinculo', 'profissionalResponsavel',
        ])->findOrFail($id);

        $this->profissionais = Profissional::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
    }

    // ── Navegação de tabs ─────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    // ── Abrir / fechar modal ──────────────────────────────────────────────────

    public function abrirModal(string $tipo, ?int $id = null): void
    {
        $this->resetModalFields();
        $this->modalTipo  = $tipo;
        $this->editandoId = $id;
        $this->modal      = true;

        if ($id) $this->carregarParaEdicao($tipo, $id);
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetModalFields();
    }

    private function resetModalFields(): void
    {
        $this->editandoId    = null;
        $this->atd_data      = $this->atd_hora = $this->atd_tipo = 'consulta';
        $this->atd_status    = 'realizado';
        $this->atd_profissional = $this->atd_local = $this->atd_observacoes = '';
        $this->ag_data_hora  = $this->ag_tipo = 'consulta';
        $this->ag_status     = 'agendado';
        $this->ag_duracao    = '30';
        $this->ag_profissional = $this->ag_observacoes = '';
        $this->doc_arquivo   = null;
        $this->doc_nome      = $this->doc_tipo = 'outros';
        $this->doc_descricao = $this->doc_data = '';
        $this->anot_titulo   = $this->anot_conteudo = '';
        $this->anot_cor      = 'blue';
        $this->anot_fixada   = false;
        $this->cob_descricao = $this->cob_valor = $this->cob_vencimento = '';
        $this->cob_pagamento = $this->cob_status = 'pendente';
        $this->cob_forma     = $this->cob_observacoes = '';
        $this->pron_data     = $this->pron_titulo = $this->pron_conteudo = '';
        $this->pron_tipo     = 'evolucao';
        $this->pron_profissional = '';
        $this->resetErrorBag();
    }

    private function carregarParaEdicao(string $tipo, int $id): void
    {
        match ($tipo) {
            'atendimento' => $this->carregarAtendimento($id),
            'agendamento' => $this->carregarAgendamento($id),
            'anotacao'    => $this->carregarAnotacao($id),
            'cobranca'    => $this->carregarCobranca($id),
            'prontuario'  => $this->carregarProntuario($id),
            default       => null,
        };
    }

    // ── Atendimentos ──────────────────────────────────────────────────────────

    private function carregarAtendimento(int $id): void
    {
        $a = Atendimento::findOrFail($id);
        $this->atd_data         = $a->data_atendimento->format('Y-m-d');
        $this->atd_hora         = $a->hora_atendimento ?? '';
        $this->atd_tipo         = $a->tipo;
        $this->atd_status       = $a->status;
        $this->atd_profissional = (string) ($a->profissional_id ?? '');
        $this->atd_local        = $a->local ?? '';
        $this->atd_observacoes  = $a->observacoes ?? '';
    }

    public function salvarAtendimento(): void
    {
        $this->validate([
            'atd_data'  => 'required|date',
            'atd_tipo'  => 'required',
            'atd_status'=> 'required',
        ]);

        $dados = [
            'paciente_id'       => $this->paciente->id,
            'profissional_id'   => $this->atd_profissional ?: null,
            'data_atendimento'  => $this->atd_data,
            'hora_atendimento'  => $this->atd_hora ?: null,
            'tipo'              => $this->atd_tipo,
            'status'            => $this->atd_status,
            'local'             => $this->atd_local ?: null,
            'observacoes'       => $this->atd_observacoes ?: null,
        ];

        $this->editandoId
            ? Atendimento::findOrFail($this->editandoId)->update($dados)
            : Atendimento::create($dados);

        $this->fecharModal();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Atendimento salvo!');
    }

    public function excluirAtendimento(int $id): void
    {
        Atendimento::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Atendimento excluído!');
    }

    // ── Agendamentos ──────────────────────────────────────────────────────────

    private function carregarAgendamento(int $id): void
    {
        $a = Agendamento::findOrFail($id);
        $this->ag_data_hora   = $a->data_hora->format('Y-m-d\TH:i');
        $this->ag_tipo        = $a->tipo;
        $this->ag_status      = $a->status;
        $this->ag_duracao     = (string) $a->duracao_minutos;
        $this->ag_profissional= (string) ($a->profissional_id ?? '');
        $this->ag_observacoes = $a->observacoes ?? '';
    }

    public function salvarAgendamento(): void
    {
        $this->validate([
            'ag_data_hora' => 'required|date',
            'ag_tipo'      => 'required',
            'ag_status'    => 'required',
        ]);

        $dados = [
            'paciente_id'     => $this->paciente->id,
            'profissional_id' => $this->ag_profissional ?: null,
            'data_hora'       => $this->ag_data_hora,
            'duracao_minutos' => (int) $this->ag_duracao ?: 30,
            'tipo'            => $this->ag_tipo,
            'status'          => $this->ag_status,
            'observacoes'     => $this->ag_observacoes ?: null,
        ];

        $this->editandoId
            ? Agendamento::findOrFail($this->editandoId)->update($dados)
            : Agendamento::create($dados);

        $this->fecharModal();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Agendamento salvo!');
    }

    public function excluirAgendamento(int $id): void
    {
        Agendamento::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Agendamento excluído!');
    }

    // ── Documentos ────────────────────────────────────────────────────────────

    public function updatedDocArquivo(): void
    {
        $this->validate(['doc_arquivo' => 'file|max:10240']);
        if (!$this->doc_nome && $this->doc_arquivo) {
            $this->doc_nome = pathinfo($this->doc_arquivo->getClientOriginalName(), PATHINFO_FILENAME);
        }
    }

    public function salvarDocumento(): void
    {
        $this->validate([
            'doc_arquivo' => $this->editandoId ? 'nullable|file|max:10240' : 'required|file|max:10240',
            'doc_nome'    => 'required|string|max:255',
            'doc_tipo'    => 'required',
        ]);

        $dados = [
            'paciente_id' => $this->paciente->id,
            'nome'        => $this->doc_nome,
            'tipo'        => $this->doc_tipo,
            'descricao'   => $this->doc_descricao ?: null,
            'data_documento' => $this->doc_data ?: null,
        ];

        if ($this->doc_arquivo) {
            if ($this->editandoId) {
                $old = DocumentoPaciente::find($this->editandoId);
                if ($old?->arquivo) Storage::disk('public')->delete($old->arquivo);
            }
            $path = $this->doc_arquivo->store("pacientes/{$this->paciente->id}/documentos", 'public');
            $dados['arquivo']   = $path;
            $dados['mime_type'] = $this->doc_arquivo->getMimeType();
            $dados['tamanho']   = $this->doc_arquivo->getSize();
        }

        $this->editandoId
            ? DocumentoPaciente::findOrFail($this->editandoId)->update($dados)
            : DocumentoPaciente::create($dados);

        $this->fecharModal();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Documento salvo!');
    }

    public function excluirDocumento(int $id): void
    {
        $doc = DocumentoPaciente::findOrFail($id);
        Storage::disk('public')->delete($doc->arquivo);
        $doc->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Documento excluído!');
    }

    // ── Anotações ─────────────────────────────────────────────────────────────

    private function carregarAnotacao(int $id): void
    {
        $a = AnotacaoPaciente::findOrFail($id);
        $this->anot_titulo   = $a->titulo ?? '';
        $this->anot_conteudo = $a->conteudo;
        $this->anot_cor      = $a->cor;
        $this->anot_fixada   = (bool) $a->fixada;
    }

    public function salvarAnotacao(): void
    {
        $this->validate(['anot_conteudo' => 'required|string']);

        $dados = [
            'paciente_id' => $this->paciente->id,
            'titulo'      => $this->anot_titulo ?: null,
            'conteudo'    => $this->anot_conteudo,
            'cor'         => $this->anot_cor,
            'fixada'      => $this->anot_fixada,
        ];

        $this->editandoId
            ? AnotacaoPaciente::findOrFail($this->editandoId)->update($dados)
            : AnotacaoPaciente::create($dados);

        $this->fecharModal();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Anotação salva!');
    }

    public function excluirAnotacao(int $id): void
    {
        AnotacaoPaciente::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Anotação excluída!');
    }

    public function toggleFixarAnotacao(int $id): void
    {
        $a = AnotacaoPaciente::findOrFail($id);
        $a->update(['fixada' => !$a->fixada]);
    }

    // ── Cobranças ─────────────────────────────────────────────────────────────

    private function carregarCobranca(int $id): void
    {
        $c = Cobranca::findOrFail($id);
        $this->cob_descricao   = $c->descricao;
        $this->cob_valor       = number_format($c->valor, 2, ',', '.');
        $this->cob_vencimento  = $c->data_vencimento->format('Y-m-d');
        $this->cob_pagamento   = $c->data_pagamento?->format('Y-m-d') ?? '';
        $this->cob_status      = $c->status;
        $this->cob_forma       = $c->forma_pagamento ?? '';
        $this->cob_observacoes = $c->observacoes ?? '';
    }

    public function salvarCobranca(): void
    {
        $this->validate([
            'cob_descricao'  => 'required|string|max:255',
            'cob_valor'      => 'required',
            'cob_vencimento' => 'required|date',
            'cob_status'     => 'required',
        ]);

        $valor = (float) str_replace(['.', ','], ['', '.'], $this->cob_valor);

        $dados = [
            'paciente_id'     => $this->paciente->id,
            'descricao'       => $this->cob_descricao,
            'valor'           => $valor,
            'data_vencimento' => $this->cob_vencimento,
            'data_pagamento'  => $this->cob_pagamento ?: null,
            'status'          => $this->cob_status,
            'forma_pagamento' => $this->cob_forma ?: null,
            'observacoes'     => $this->cob_observacoes ?: null,
        ];

        $this->editandoId
            ? Cobranca::findOrFail($this->editandoId)->update($dados)
            : Cobranca::create($dados);

        $this->fecharModal();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Cobrança salva!');
    }

    public function excluirCobranca(int $id): void
    {
        Cobranca::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Cobrança excluída!');
    }

    public function marcarPago(int $id): void
    {
        Cobranca::findOrFail($id)->update([
            'status'         => 'pago',
            'data_pagamento' => now()->toDateString(),
        ]);
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Cobrança marcada como paga!');
    }

    // ── Prontuário ────────────────────────────────────────────────────────────

    private function carregarProntuario(int $id): void
    {
        $p = ProntuarioEntrada::findOrFail($id);
        $this->pron_data         = $p->data_entrada->format('Y-m-d');
        $this->pron_tipo         = $p->tipo;
        $this->pron_titulo       = $p->titulo ?? '';
        $this->pron_conteudo     = $p->conteudo;
        $this->pron_profissional = (string) ($p->profissional_id ?? '');
    }

    public function salvarProntuario(): void
    {
        $this->validate([
            'pron_data'     => 'required|date',
            'pron_tipo'     => 'required',
            'pron_conteudo' => 'required|string',
        ]);

        $dados = [
            'paciente_id'    => $this->paciente->id,
            'profissional_id'=> $this->pron_profissional ?: null,
            'data_entrada'   => $this->pron_data,
            'tipo'           => $this->pron_tipo,
            'titulo'         => $this->pron_titulo ?: null,
            'conteudo'       => $this->pron_conteudo,
        ];

        $this->editandoId
            ? ProntuarioEntrada::findOrFail($this->editandoId)->update($dados)
            : ProntuarioEntrada::create($dados);

        $this->fecharModal();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Entrada salva no prontuário!');
    }

    public function excluirProntuario(int $id): void
    {
        ProntuarioEntrada::findOrFail($id)->delete();
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Entrada excluída!');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $pid = $this->paciente->id;

        $atendimentos = Atendimento::where('paciente_id', $pid)
            ->with('profissional')->orderByDesc('data_atendimento')->paginate(10, ['*'], 'atd_page');

        $agendamentos = Agendamento::where('paciente_id', $pid)
            ->with('profissional')->orderByDesc('data_hora')->paginate(10, ['*'], 'ag_page');

        $documentos = DocumentoPaciente::where('paciente_id', $pid)
            ->orderByDesc('created_at')->paginate(12, ['*'], 'doc_page');

        $anotacoes = AnotacaoPaciente::where('paciente_id', $pid)
            ->orderByDesc('fixada')->orderByDesc('created_at')->get();

        $cobrancas = Cobranca::where('paciente_id', $pid)
            ->orderByDesc('data_vencimento')->paginate(10, ['*'], 'cob_page');

        $prontuario = ProntuarioEntrada::where('paciente_id', $pid)
            ->with('profissional')->orderByDesc('data_entrada')->paginate(10, ['*'], 'pron_page');

        // Totais financeiros
        $finTotais = [
            'total'    => Cobranca::where('paciente_id', $pid)->sum('valor'),
            'pago'     => Cobranca::where('paciente_id', $pid)->where('status','pago')->sum('valor'),
            'pendente' => Cobranca::where('paciente_id', $pid)->whereIn('status',['pendente','vencido'])->sum('valor'),
        ];

        // Timeline: agrega eventos de todas as tabelas
        $timeline = collect()
            ->merge($atendimentos->getCollection()->map(fn($a) => [
                'tipo' => 'atendimento', 'data' => $a->data_atendimento,
                'texto' => ucfirst($a->tipo) . ($a->profissional ? ' — ' . $a->profissional->nome : ''),
                'status' => $a->status, 'id' => $a->id,
            ]))
            ->merge($agendamentos->getCollection()->map(fn($a) => [
                'tipo' => 'agendamento', 'data' => $a->data_hora->toDateString(),
                'texto' => 'Agendamento: ' . ucfirst($a->tipo),
                'status' => $a->status, 'id' => $a->id,
            ]))
            ->merge($cobrancas->getCollection()->map(fn($c) => [
                'tipo' => 'cobranca', 'data' => $c->data_vencimento,
                'texto' => $c->descricao . ' — R$ ' . number_format($c->valor, 2, ',', '.'),
                'status' => $c->status, 'id' => $c->id,
            ]))
            ->merge(ProntuarioEntrada::where('paciente_id', $pid)->latest('data_entrada')->take(5)->get()->map(fn($p) => [
                'tipo' => 'prontuario', 'data' => $p->data_entrada,
                'texto' => ucfirst($p->tipo) . ($p->titulo ? ': ' . $p->titulo : ''),
                'status' => 'ok', 'id' => $p->id,
            ]))
            ->sortByDesc('data')
            ->values()
            ->take(30);

        return view('livewire.clientes.detalhes-paciente', compact(
            'atendimentos','agendamentos','documentos','anotacoes',
            'cobrancas','prontuario','finTotais','timeline'
        ))->layout('layouts.app', ['title' => $this->paciente->nome]);
    }
}
