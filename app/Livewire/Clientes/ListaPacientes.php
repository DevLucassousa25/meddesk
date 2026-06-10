<?php

namespace App\Livewire\Clientes;

use App\Models\Agendamento;
use App\Models\Atendimento;
use App\Models\Convenio;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\TipoVinculo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use App\Traits\UsaFilialAtiva;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListaPacientes extends Component
{
    use WithPagination, WithFileUploads, UsaFilialAtiva;

    // ── Listagem / Filtros ───────────────────────────────────────────────────
    public string $busca                 = '';
    public string $filtroStatus          = '';   // ativo | inativo | ''
    public string $filtroConvenio        = '';
    public string $filtroProfissional    = '';
    public string $filtroGenero          = '';
    public string $filtroInadimplente    = '';   // '' | '1'
    public string $filtroDe              = '';
    public string $filtroAte             = '';
    public string $filtroTipoVinculo     = '';
    public string $filtroFaixaEtaria     = '';   // '' | '0-17' | '18-35' | '36-60' | '60+'
    public bool   $filtroAniversariantes = false;
    public string $filtroSemConsulta     = '';   // '' | '30' | '60' | '90' | '180'
    public int    $porPagina             = 15;

    // ── Ordenação ────────────────────────────────────────────────────────────
    public string $sortBy  = 'nome';
    public string $sortDir = 'asc';

    // ── Visualização ─────────────────────────────────────────────────────────
    public string $visualizacao = 'tabela';  // tabela | cards

    // ── Seleção em massa ─────────────────────────────────────────────────────
    public array $selecionados = [];

    // ── Modal cadastro/edição ─────────────────────────────────────────────────
    public bool    $modalAberto = false;
    public ?int    $pacienteId  = null;

    // ── Modal importação ──────────────────────────────────────────────────────
    public bool   $modalImportacao  = false;
    public $arquivoImport           = null;
    public array  $importResultado  = [];

    // ── Campos do formulário ──────────────────────────────────────────────────
    public $foto                         = null;
    public string $foto_atual            = '';
    public string $codigo                = '';
    public string $status_cliente        = 'ativo';
    public string $nome                  = '';
    public string $cpf                   = '';
    public string $cnpj                  = '';
    public string $genero                = '';
    public string $rg                    = '';
    public bool   $ativo                 = true;
    public string $data_nascimento       = '';
    public string $idade                 = '';
    public string $data_ativacao         = '';
    public string $data_inativacao       = '';
    public string $cep                   = '';
    public string $empresa               = '';
    public string $endereco              = '';
    public string $numero                = '';
    public string $cidade                = '';
    public string $uf                    = '';
    public string $complemento           = '';
    public string $bairro                = '';
    public string $telefone1             = '';
    public string $celular1              = '';
    public string $celular2              = '';
    public string $email                 = '';
    public string $tipo_vinculo_id       = '';
    public string $profissional_responsavel_id = '';
    public string $convenio_id           = '';
    public string $informacoes_iniciais  = '';
    public bool   $enviar_whatsapp       = false;
    public string $mensagens_por         = '';
    public bool   $whatsapp_ativo        = false;
    public bool   $sms_ativo             = false;
    public bool   $numero_internacional  = false;
    public bool   $inadimplente          = false;
    public bool   $tem_guia_finalizada   = false;
    public bool   $devendo_guia          = false;
    public bool   $matricula_trancada    = false;
    public bool   $controle_matricula    = false;
    public bool   $financeiro_pendente   = false;
    public bool   $atencao_informacoes   = false;
    public string $inicio_plano          = '';
    public string $fim_plano             = '';

    // CEP
    public bool   $cep_loading    = false;
    public bool   $cep_encontrado = false;
    public string $cep_erro       = '';

    // Auxiliares
    public array $convenios     = [];
    public array $tiposVinculo  = [];
    public array $profissionais = [];

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->carregarAuxiliares();
    }

    private function carregarAuxiliares(): void
    {
        $this->convenios     = Convenio::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
        $this->tiposVinculo  = TipoVinculo::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
        $this->profissionais = Profissional::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
    }

    // ── Filtros / Paginação ────────────────────────────────────────────────────

    public function updatingBusca(): void               { $this->resetPage(); }
    public function updatingFiltroStatus(): void        { $this->resetPage(); }
    public function updatingFiltroConvenio(): void      { $this->resetPage(); }
    public function updatingFiltroProfissional(): void  { $this->resetPage(); }
    public function updatingFiltroGenero(): void        { $this->resetPage(); }
    public function updatingFiltroInadimplente(): void  { $this->resetPage(); }
    public function updatingFiltroDe(): void            { $this->resetPage(); }
    public function updatingFiltroAte(): void           { $this->resetPage(); }
    public function updatingFiltroTipoVinculo(): void   { $this->resetPage(); }
    public function updatingFiltroFaixaEtaria(): void   { $this->resetPage(); }
    public function updatingFiltroAniversariantes(): void { $this->resetPage(); }
    public function updatingFiltroSemConsulta(): void   { $this->resetPage(); }

    public function limparFiltros(): void
    {
        $this->busca                 = '';
        $this->filtroStatus          = '';
        $this->filtroConvenio        = '';
        $this->filtroProfissional    = '';
        $this->filtroGenero          = '';
        $this->filtroInadimplente    = '';
        $this->filtroDe              = '';
        $this->filtroAte             = '';
        $this->filtroTipoVinculo     = '';
        $this->filtroFaixaEtaria     = '';
        $this->filtroAniversariantes = false;
        $this->filtroSemConsulta     = '';
        $this->resetPage();
    }

    public function temFiltrosAtivos(): bool
    {
        return $this->busca !== ''
            || $this->filtroStatus !== ''
            || $this->filtroConvenio !== ''
            || $this->filtroProfissional !== ''
            || $this->filtroGenero !== ''
            || $this->filtroInadimplente !== ''
            || $this->filtroDe !== ''
            || $this->filtroAte !== ''
            || $this->filtroTipoVinculo !== ''
            || $this->filtroFaixaEtaria !== ''
            || $this->filtroAniversariantes
            || $this->filtroSemConsulta !== '';
    }

    // ── Ordenação ─────────────────────────────────────────────────────────────

    public function sortarPor(string $coluna): void
    {
        if ($this->sortBy === $coluna) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $coluna;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    // ── Query centralizada ─────────────────────────────────────────────────────

    private function construirQuery()
    {
        return Paciente::query()
            ->when($this->temFilial() && ! $this->filtroProfissional,
                fn($q) => $this->scopeFilialPaciente($q)
            )
            ->select('pacientes.*')
            ->addSelect([
                'ultima_consulta' => Atendimento::select('data_atendimento')
                    ->whereColumn('paciente_id', 'pacientes.id')
                    ->orderByDesc('data_atendimento')
                    ->limit(1),
                'proxima_consulta' => Agendamento::select('data_hora')
                    ->whereColumn('paciente_id', 'pacientes.id')
                    ->where('data_hora', '>=', now())
                    ->whereIn('status', ['agendado', 'confirmado'])
                    ->orderBy('data_hora')
                    ->limit(1),
            ])
            ->when($this->busca, fn($q) => $q->where(fn($q) =>
                $q->where('nome',    'like', "%{$this->busca}%")
                  ->orWhere('cpf',   'like', "%{$this->busca}%")
                  ->orWhere('email', 'like', "%{$this->busca}%")
                  ->orWhere('codigo','like', "%{$this->busca}%")
            ))
            ->when($this->filtroStatus === 'ativo',    fn($q) => $q->where('ativo', true))
            ->when($this->filtroStatus === 'inativo',  fn($q) => $q->where('ativo', false))
            ->when($this->filtroConvenio,              fn($q) => $q->where('convenio_id', $this->filtroConvenio))
            ->when($this->filtroProfissional,          fn($q) => $q->where('profissional_responsavel_id', $this->filtroProfissional))
            ->when($this->filtroGenero,                fn($q) => $q->where('genero', $this->filtroGenero))
            ->when($this->filtroInadimplente === '1',  fn($q) => $q->where('inadimplente', true))
            ->when($this->filtroTipoVinculo,           fn($q) => $q->where('tipo_vinculo_id', $this->filtroTipoVinculo))
            ->when($this->filtroDe,                    fn($q) => $q->whereDate('created_at', '>=', $this->filtroDe))
            ->when($this->filtroAte,                   fn($q) => $q->whereDate('created_at', '<=', $this->filtroAte))
            ->when($this->filtroAniversariantes,       fn($q) => $q->whereNotNull('data_nascimento')->whereMonth('data_nascimento', now()->month))
            ->when($this->filtroFaixaEtaria === '0-17',  fn($q) => $q->whereNotNull('data_nascimento')->where('data_nascimento', '>',  now()->subYears(18)))
            ->when($this->filtroFaixaEtaria === '18-35', fn($q) => $q->whereNotNull('data_nascimento')->whereBetween('data_nascimento', [now()->subYears(36), now()->subYears(18)]))
            ->when($this->filtroFaixaEtaria === '36-60', fn($q) => $q->whereNotNull('data_nascimento')->whereBetween('data_nascimento', [now()->subYears(61), now()->subYears(36)]))
            ->when($this->filtroFaixaEtaria === '60+',   fn($q) => $q->whereNotNull('data_nascimento')->where('data_nascimento', '<=', now()->subYears(60)))
            ->when($this->filtroSemConsulta, fn($q) =>
                $q->whereDoesntHave('atendimentos', fn($a) =>
                    $a->where('data_atendimento', '>=', now()->subDays((int) $this->filtroSemConsulta))
                )
            )
            ->with(['convenio', 'profissionalResponsavel', 'tipoVinculo'])
            ->when($this->sortBy === 'nome',           fn($q) => $q->orderBy('nome', $this->sortDir))
            ->when($this->sortBy === 'created_at',     fn($q) => $q->orderBy('created_at', $this->sortDir))
            ->when($this->sortBy === 'ultima_consulta',fn($q) => $q->orderBy('ultima_consulta', $this->sortDir))
            ->when(!in_array($this->sortBy, ['nome', 'created_at', 'ultima_consulta']), fn($q) => $q->orderBy('nome', 'asc'));
    }

    // ── Seleção em massa ──────────────────────────────────────────────────────

    public function toggleSelecionado(int $id): void
    {
        if (in_array($id, $this->selecionados)) {
            $this->selecionados = array_values(array_filter($this->selecionados, fn($i) => $i !== $id));
        } else {
            $this->selecionados[] = $id;
        }
    }

    public function selecionarTodosVisiveis(array $ids): void
    {
        $todosJaSelecionados = count(array_intersect($ids, $this->selecionados)) === count($ids);
        if ($todosJaSelecionados) {
            $this->selecionados = array_values(array_filter($this->selecionados, fn($i) => !in_array($i, $ids)));
        } else {
            $this->selecionados = array_values(array_unique(array_merge($this->selecionados, $ids)));
        }
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
    }

    public function inativarSelecionados(): void
    {
        if (empty($this->selecionados)) return;
        $count = count($this->selecionados);
        Paciente::whereIn('id', $this->selecionados)->update(['ativo' => false]);
        $this->selecionados = [];
        $this->dispatch('modal-success', title: 'Concluído!', message: "{$count} paciente(s) inativado(s) com sucesso.");
    }

    public function ativarSelecionados(): void
    {
        if (empty($this->selecionados)) return;
        $count = count($this->selecionados);
        Paciente::whereIn('id', $this->selecionados)->update(['ativo' => true]);
        $this->selecionados = [];
        $this->dispatch('modal-success', title: 'Concluído!', message: "{$count} paciente(s) ativado(s) com sucesso.");
    }

    #[Computed]
    public function selecionadosTodosInativos(): bool
    {
        if (empty($this->selecionados)) return false;
        return !Paciente::whereIn('id', $this->selecionados)->where('ativo', true)->exists();
    }

    // ── Export ────────────────────────────────────────────────────────────────

    #[Renderless]
    public function exportarCsv(): mixed
    {
        $pacientes = $this->construirQuery()->get();
        $filename  = 'pacientes-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($pacientes) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, ['Código','Nome','CPF','E-mail','Telefone','Celular','Data Nasc.','Gênero','Convênio','Tipo Vínculo','Profissional','Status','Inadimplente','Cidade','UF','Cadastrado em'], ';');
            foreach ($pacientes as $p) {
                fputcsv($out, [
                    $p->codigo,
                    $p->nome,
                    $p->cpf,
                    $p->email,
                    $p->telefone1,
                    $p->celular1,
                    $p->data_nascimento?->format('d/m/Y'),
                    match($p->genero) {
                        'masculino'    => 'Masculino',
                        'feminino'     => 'Feminino',
                        'outro'        => 'Outro',
                        'nao_informado'=> 'Não informado',
                        default        => '',
                    },
                    $p->convenio?->nome,
                    $p->tipoVinculo?->nome,
                    $p->profissionalResponsavel?->nome,
                    $p->ativo ? 'Ativo' : 'Inativo',
                    $p->inadimplente ? 'Sim' : 'Não',
                    $p->cidade,
                    $p->uf,
                    $p->created_at->format('d/m/Y'),
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    #[Renderless]
    public function exportarSelecionados(): mixed
    {
        if (empty($this->selecionados)) return null;

        $pacientes = Paciente::whereIn('id', $this->selecionados)
            ->with(['convenio', 'profissionalResponsavel', 'tipoVinculo'])
            ->get();

        $filename = 'pacientes-selecionados-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($pacientes) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Código','Nome','CPF','E-mail','Telefone','Celular','Data Nasc.','Gênero','Convênio','Tipo Vínculo','Profissional','Status','Inadimplente','Cidade','UF','Cadastrado em'], ';');
            foreach ($pacientes as $p) {
                fputcsv($out, [
                    $p->codigo, $p->nome, $p->cpf, $p->email, $p->telefone1, $p->celular1,
                    $p->data_nascimento?->format('d/m/Y'),
                    match($p->genero) {
                        'masculino' => 'Masculino', 'feminino' => 'Feminino',
                        'outro' => 'Outro', 'nao_informado' => 'Não informado', default => '',
                    },
                    $p->convenio?->nome, $p->tipoVinculo?->nome,
                    $p->profissionalResponsavel?->nome,
                    $p->ativo ? 'Ativo' : 'Inativo', $p->inadimplente ? 'Sim' : 'Não',
                    $p->cidade, $p->uf, $p->created_at->format('d/m/Y'),
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Import ────────────────────────────────────────────────────────────────

    public function abrirImportacao(): void
    {
        $this->modalImportacao = true;
        $this->arquivoImport   = null;
        $this->importResultado = [];
    }

    public function fecharImportacao(): void
    {
        $this->modalImportacao = false;
        $this->arquivoImport   = null;
        $this->importResultado = [];
        $this->resetErrorBag('arquivoImport');
    }

    public function processarImportacao(): void
    {
        $this->validate(['arquivoImport' => 'required|file|mimes:csv,txt|max:5120']);

        $path    = $this->arquivoImport->getRealPath();
        $handle  = fopen($path, 'r');
        // Remove BOM se existir
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $header   = null;
        $imported = 0;
        $errors   = [];
        $row      = 0;

        while (($line = fgetcsv($handle, 2000, ';')) !== false) {
            $row++;
            if ($row === 1) {
                $header = array_map(fn($h) => mb_strtolower(trim($h)), $line);
                continue;
            }
            if (empty(array_filter($line))) continue;

            $data = array_combine(
                array_slice($header, 0, count($line)),
                array_map('trim', $line)
            );

            $nome = $data['nome'] ?? '';
            if (empty($nome)) {
                $errors[] = "Linha {$row}: nome obrigatório.";
                continue;
            }

            try {
                $nascimento = null;
                $nascRaw = $data['data nasc.'] ?? $data['data_nascimento'] ?? '';
                if ($nascRaw) {
                    try {
                        $nascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $nascRaw)->format('Y-m-d');
                    } catch (\Throwable) {}
                }

                Paciente::create([
                    'codigo'         => Paciente::gerarCodigo(),
                    'nome'           => $nome,
                    'cpf'            => preg_replace('/\D/', '', $data['cpf'] ?? '') ?: null,
                    'email'          => $data['e-mail'] ?? $data['email'] ?? null,
                    'telefone1'      => $data['telefone'] ?? null,
                    'celular1'       => $data['celular']  ?? null,
                    'data_nascimento'=> $nascimento,
                    'genero'         => $data['gênero'] ?? $data['genero'] ?? null,
                    'cidade'         => $data['cidade'] ?? null,
                    'uf'             => $data['uf']     ?? null,
                    'ativo'          => true,
                    'status_cliente' => 'ativo',
                ]);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Linha {$row}: {$e->getMessage()}";
            }
        }

        fclose($handle);

        $this->importResultado = [
            'importados' => $imported,
            'erros'      => $errors,
        ];
        $this->arquivoImport = null;
    }


    public function navegaFormularioCadastro(): void
    {
        $this->redirectRoute('pacientes.cadastro');
    }

    // ── Modal: Abrir / Fechar ─────────────────────────────────────────────────

    public function abrirNovo(): void
    {
        $this->resetFormulario();
        $this->modalAberto = true;
    }

    public function abrirEditar(int $id): void
    {
        $this->resetFormulario();
        $this->pacienteId = $id;
        $this->carregarPaciente($id);
        $this->modalAberto = true;
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetFormulario();
    }

    private function resetFormulario(): void
    {
        $this->pacienteId                  = null;
        $this->foto                        = null;
        $this->foto_atual                  = '';
        $this->codigo                      = '';
        $this->status_cliente              = 'ativo';
        $this->nome                        = '';
        $this->cpf                         = '';
        $this->cnpj                        = '';
        $this->genero                      = '';
        $this->rg                          = '';
        $this->ativo                       = true;
        $this->data_nascimento             = '';
        $this->idade                       = '';
        $this->data_ativacao               = '';
        $this->data_inativacao             = '';
        $this->cep                         = '';
        $this->empresa                     = '';
        $this->endereco                    = '';
        $this->numero                      = '';
        $this->cidade                      = '';
        $this->uf                          = '';
        $this->complemento                 = '';
        $this->bairro                      = '';
        $this->telefone1                   = '';
        $this->celular1                    = '';
        $this->celular2                    = '';
        $this->email                       = '';
        $this->tipo_vinculo_id             = '';
        $this->profissional_responsavel_id = '';
        $this->convenio_id                 = '';
        $this->informacoes_iniciais        = '';
        $this->enviar_whatsapp             = false;
        $this->mensagens_por               = '';
        $this->whatsapp_ativo              = false;
        $this->sms_ativo                   = false;
        $this->numero_internacional        = false;
        $this->inadimplente                = false;
        $this->tem_guia_finalizada         = false;
        $this->devendo_guia                = false;
        $this->matricula_trancada          = false;
        $this->controle_matricula          = false;
        $this->financeiro_pendente         = false;
        $this->atencao_informacoes         = false;
        $this->inicio_plano                = '';
        $this->fim_plano                   = '';
        $this->cep_loading                 = false;
        $this->cep_encontrado              = false;
        $this->cep_erro                    = '';
        $this->resetErrorBag();
    }

    private function carregarPaciente(int $id): void
    {
        $p = Paciente::findOrFail($id);

        $this->codigo                      = $p->codigo ?? '';
        $this->status_cliente              = $p->status_cliente ?? 'ativo';
        $this->nome                        = $p->nome;
        $this->cpf                         = $p->cpf ?? '';
        $this->cnpj                        = $p->cnpj ?? '';
        $this->genero                      = $p->genero ?? '';
        $this->rg                          = $p->rg ?? '';
        $this->ativo                       = (bool) $p->ativo;
        $this->data_nascimento             = $p->data_nascimento?->format('Y-m-d') ?? '';
        $this->data_ativacao               = $p->data_ativacao?->format('Y-m-d') ?? '';
        $this->data_inativacao             = $p->data_inativacao?->format('Y-m-d') ?? '';
        $this->cep                         = $p->cep ?? '';
        $this->empresa                     = $p->empresa ?? '';
        $this->endereco                    = $p->endereco ?? '';
        $this->numero                      = $p->numero ?? '';
        $this->cidade                      = $p->cidade ?? '';
        $this->uf                          = $p->uf ?? '';
        $this->complemento                 = $p->complemento ?? '';
        $this->bairro                      = $p->bairro ?? '';
        $this->telefone1                   = $p->telefone1 ?? '';
        $this->celular1                    = $p->celular1 ?? '';
        $this->celular2                    = $p->celular2 ?? '';
        $this->email                       = $p->email ?? '';
        $this->tipo_vinculo_id             = (string) ($p->tipo_vinculo_id ?? '');
        $this->profissional_responsavel_id = (string) ($p->profissional_responsavel_id ?? '');
        $this->convenio_id                 = (string) ($p->convenio_id ?? '');
        $this->informacoes_iniciais        = $p->informacoes_iniciais ?? '';
        $this->enviar_whatsapp             = (bool) $p->enviar_whatsapp;
        $this->mensagens_por               = $p->mensagens_por ?? '';
        $this->whatsapp_ativo              = (bool) $p->whatsapp_ativo;
        $this->sms_ativo                   = (bool) $p->sms_ativo;
        $this->numero_internacional        = (bool) $p->numero_internacional;
        $this->inadimplente                = (bool) $p->inadimplente;
        $this->tem_guia_finalizada         = (bool) $p->tem_guia_finalizada;
        $this->devendo_guia                = (bool) $p->devendo_guia;
        $this->matricula_trancada          = (bool) $p->matricula_trancada;
        $this->controle_matricula          = (bool) $p->controle_matricula;
        $this->financeiro_pendente         = (bool) $p->financeiro_pendente;
        $this->atencao_informacoes         = (bool) $p->atencao_informacoes;
        $this->inicio_plano                = $p->inicio_plano?->format('Y-m-d') ?? '';
        $this->fim_plano                   = $p->fim_plano?->format('Y-m-d') ?? '';
        $this->foto_atual                  = $p->foto ?? '';

        $this->calcularIdade();
    }

    // ── CEP ───────────────────────────────────────────────────────────────────

    public function buscarCep(string $cepInput = ''): void
    {
        $cep = preg_replace('/\D/', '', $cepInput ?: $this->cep);
        $this->cep            = $cep;
        $this->cep_erro       = '';
        $this->cep_encontrado = false;

        if (strlen($cep) !== 8) return;

        $this->cep_loading = true;
        try {
            $res  = Http::timeout(6)->get("https://viacep.com.br/ws/{$cep}/json/");
            $data = $res->json();
            if (!$res->successful() || !empty($data['erro'])) {
                $this->cep_erro = empty($data['erro']) ? 'Erro ao consultar o CEP.' : 'CEP não encontrado.';
                return;
            }
            $this->endereco       = $data['logradouro'] ?? '';
            $this->bairro         = $data['bairro']     ?? '';
            $this->cidade         = $data['localidade'] ?? '';
            $this->uf             = $data['uf']         ?? '';
            $this->cep_encontrado = true;
        } catch (\Throwable) {
            $this->cep_erro = 'Não foi possível consultar o CEP.';
        } finally {
            $this->cep_loading = false;
        }
    }

    // ── Idade ──────────────────────────────────────────────────────────────────

    public function updatedDataNascimento(): void { $this->calcularIdade(); }

    public function calcularIdade(): void
    {
        $this->idade = $this->data_nascimento
            ? (string) \Carbon\Carbon::parse($this->data_nascimento)->age
            : '';
    }

    // ── Foto ───────────────────────────────────────────────────────────────────

    public function updatedFoto(): void { $this->validate(['foto' => 'image|max:2048']); }

    public function removerFoto(): void
    {
        if ($this->foto_atual) Storage::disk('public')->delete($this->foto_atual);
        $this->foto       = null;
        $this->foto_atual = '';
    }

    // ── Salvar ─────────────────────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate([
            'nome'  => 'required|string|max:255',
            'cpf'   => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'foto'  => 'nullable|image|max:2048',
        ]);

        if ($this->foto) {
            if ($this->foto_atual) Storage::disk('public')->delete($this->foto_atual);
            $this->foto_atual = $this->foto->store('pacientes/fotos', 'public');
            $this->foto = null;
        }

        $dados = [
            'status_cliente'              => $this->status_cliente,
            'nome'                        => $this->nome,
            'cpf'                         => preg_replace('/\D/', '', $this->cpf) ?: null,
            'cnpj'                        => preg_replace('/\D/', '', $this->cnpj) ?: null,
            'genero'                      => $this->genero ?: null,
            'rg'                          => $this->rg ?: null,
            'ativo'                       => $this->ativo,
            'data_nascimento'             => $this->data_nascimento ?: null,
            'data_ativacao'               => $this->data_ativacao ?: null,
            'data_inativacao'             => $this->data_inativacao ?: null,
            'cep'                         => preg_replace('/\D/', '', $this->cep) ?: null,
            'empresa'                     => $this->empresa ?: null,
            'endereco'                    => $this->endereco ?: null,
            'numero'                      => $this->numero ?: null,
            'cidade'                      => $this->cidade ?: null,
            'uf'                          => $this->uf ?: null,
            'complemento'                 => $this->complemento ?: null,
            'bairro'                      => $this->bairro ?: null,
            'telefone1'                   => $this->telefone1 ?: null,
            'celular1'                    => $this->celular1 ?: null,
            'celular2'                    => $this->celular2 ?: null,
            'email'                       => $this->email ?: null,
            'tipo_vinculo_id'             => $this->tipo_vinculo_id ?: null,
            'profissional_responsavel_id' => $this->profissional_responsavel_id ?: null,
            'convenio_id'                 => $this->convenio_id ?: null,
            'informacoes_iniciais'        => $this->informacoes_iniciais ?: null,
            'enviar_whatsapp'             => $this->enviar_whatsapp,
            'mensagens_por'               => $this->mensagens_por ?: null,
            'whatsapp_ativo'              => $this->whatsapp_ativo,
            'sms_ativo'                   => $this->sms_ativo,
            'numero_internacional'        => $this->numero_internacional,
            'inadimplente'                => $this->inadimplente,
            'tem_guia_finalizada'         => $this->tem_guia_finalizada,
            'devendo_guia'                => $this->devendo_guia,
            'matricula_trancada'          => $this->matricula_trancada,
            'controle_matricula'          => $this->controle_matricula,
            'financeiro_pendente'         => $this->financeiro_pendente,
            'atencao_informacoes'         => $this->atencao_informacoes,
            'inicio_plano'                => $this->inicio_plano ?: null,
            'fim_plano'                   => $this->fim_plano ?: null,
            'foto'                        => $this->foto_atual ?: null,
        ];

        if ($this->pacienteId) {
            Paciente::findOrFail($this->pacienteId)->update($dados);
            $this->dispatch('modal-success', title: 'Concluído!', message: 'Paciente atualizado com sucesso!');
        } else {
            $dados['codigo'] = Paciente::gerarCodigo();
            Paciente::create($dados);
            $this->dispatch('modal-success', title: 'Concluído!', message: 'Paciente cadastrado com sucesso!');
        }

        $this->fecharModal();
    }

    // ── Excluir ────────────────────────────────────────────────────────────────

    public function excluir(int $id): void
    {
        $p = Paciente::findOrFail($id);
        if ($p->foto) Storage::disk('public')->delete($p->foto);
        $p->delete();
        // Remove da seleção se estava selecionado
        $this->selecionados = array_values(array_filter($this->selecionados, fn($i) => $i !== $id));
        $this->dispatch('modal-success', title: 'Concluído!', message: 'Paciente excluído com sucesso!');
    }

    // ── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        $pacientes = $this->construirQuery()->paginate($this->porPagina);

        $baseStats = Paciente::query()
            ->when($this->temFilial(), fn($q) => $this->scopeFilialPaciente($q));

        $stats = [
            'total'         => (clone $baseStats)->count(),
            'ativos'        => (clone $baseStats)->where('ativo', true)->count(),
            'inativos'      => (clone $baseStats)->where('ativo', false)->count(),
            'inadimplentes' => (clone $baseStats)->where('inadimplente', true)->count(),
        ];

        $totalFiltrado = $this->construirQuery()->count();

        return view('livewire.clientes.lista-pacientes', compact('pacientes', 'stats', 'totalFiltrado'))
            ->layout('layouts.app', ['title' => 'Pacientes']);
    }
}
