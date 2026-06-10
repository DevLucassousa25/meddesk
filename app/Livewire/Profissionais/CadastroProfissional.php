<?php

namespace App\Livewire\Profissionais;

use App\Models\Especialidade;
use App\Models\Profissional;
use App\Models\ProfissionalUnidade;
use App\Models\Unidade;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CadastroProfissional extends Component
{
    use WithFileUploads;

    // ── IDs ──────────────────────────────────────────────────────────────────
    public ?int $profissionalId = null;

    // ── Informações Gerais ────────────────────────────────────────────────────
    public string $nome               = '';
    public string $identificacao      = '';
    public string $senha_celular      = '';
    public string $cpf                = '';
    public string $rg                 = '';
    public string $conselho_profissional = '';
    public string $cep                = '';
    public string $endereco           = '';
    public string $numero             = '';
    public string $bairro             = '';
    public string $cidade             = '';
    public string $uf                 = '';
    public string $email              = '';
    public string $telefone           = '';
    public string $celular1           = '';
    public string $celular2           = '';
    public string $data_nascimento    = '';
    public bool   $ativo              = true;
    public int    $ordem_agenda       = 1;
    public bool   $pode_estender_horarios   = false;
    public bool   $horarios_flexiveis       = false;
    public bool   $receber_lembrete_evoluir = false;
    public $foto         = null;
    public string $foto_atual = '';

    // ── CEP ──────────────────────────────────────────────────────────────────
    public bool   $cep_loading    = false;
    public bool   $cep_encontrado = false;
    public string $cep_erro       = '';

    // ── Comissão ─────────────────────────────────────────────────────────────
    public bool   $comissao_personalizada = false;
    public string $percentual_comissao    = '';

    // ── Especialidades ────────────────────────────────────────────────────────
    public array $especialidadesDisponiveis = [];
    public array $unidadesDisponiveis       = [];
    public array $especialidadesSelecionadas = [];  // [{id, nome, empresa}]
    public string $especialidade_nova_id = '';
    public bool  $modal_especialidade = false;

    // ── Unidades ──────────────────────────────────────────────────────────────
    public array  $unidades = [];   // [{id?, unidade, dias: {seg:{ativo,inicio,fim}, ...}}]
    public bool   $modal_unidade = false;
    public string $unidade_nova       = '';
    public array  $unidade_dias       = [];
    public ?int   $unidade_edit_index = null;

    private static function diasPadrao(): array
    {
        return [
            'seg' => ['label' => 'Seg', 'ativo' => false, 'inicio' => '', 'fim' => ''],
            'ter' => ['label' => 'Ter', 'ativo' => false, 'inicio' => '', 'fim' => ''],
            'qua' => ['label' => 'Qua', 'ativo' => false, 'inicio' => '', 'fim' => ''],
            'qui' => ['label' => 'Qui', 'ativo' => false, 'inicio' => '', 'fim' => ''],
            'sex' => ['label' => 'Sex', 'ativo' => false, 'inicio' => '', 'fim' => ''],
            'sab' => ['label' => 'Sáb', 'ativo' => false, 'inicio' => '', 'fim' => ''],
            'dom' => ['label' => 'Dom', 'ativo' => false, 'inicio' => '', 'fim' => ''],
        ];
    }

    // ── Permissões App ────────────────────────────────────────────────────────
    public bool $perm_ver_somente_seus    = false;
    public bool $perm_fluxo_caixa         = false;
    public bool $perm_agendar_celular      = false;
    public bool $perm_editar_agenda        = false;
    public bool $perm_alterar_status       = false;
    public bool $perm_acessar_cadastro     = false;
    public bool $perm_editar_recebimentos  = false;
    public bool $perm_remover_recebimentos = false;

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(?int $profissionalId = null): void
    {
        $this->especialidadesDisponiveis = Especialidade::ativas()->orderBy('nome')->get(['id','nome','empresa'])->toArray();
        $this->unidadesDisponiveis       = Unidade::ativas()->orderBy('nome')->get(['id','nome'])->toArray();

        if ($profissionalId) {
            $this->profissionalId = $profissionalId;
            $this->carregarProfissional($profissionalId);
        }
    }

    private function carregarProfissional(int $id): void
    {
        $p = Profissional::with(['especialidades', 'unidades'])->findOrFail($id);

        $this->nome               = $p->nome ?? '';
        $this->identificacao      = $p->identificacao ?? '';
        $this->senha_celular      = $p->senha_celular ?? '';
        $this->cpf                = $p->cpf ?? '';
        $this->rg                 = $p->rg ?? '';
        $this->conselho_profissional = $p->conselho_profissional ?? '';
        $this->cep                = $p->cep ?? '';
        $this->endereco           = $p->endereco ?? '';
        $this->numero             = $p->numero ?? '';
        $this->bairro             = $p->bairro ?? '';
        $this->cidade             = $p->cidade ?? '';
        $this->uf                 = $p->uf ?? '';
        $this->email              = $p->email ?? '';
        $this->telefone           = $p->telefone ?? '';
        $this->celular1           = $p->celular1 ?? '';
        $this->celular2           = $p->celular2 ?? '';
        $this->data_nascimento    = $p->data_nascimento ? $p->data_nascimento->format('Y-m-d') : '';
        $this->ativo              = (bool) $p->ativo;
        $this->ordem_agenda       = $p->ordem_agenda ?? 1;
        $this->pode_estender_horarios   = (bool) $p->pode_estender_horarios;
        $this->horarios_flexiveis       = (bool) $p->horarios_flexiveis;
        $this->receber_lembrete_evoluir = (bool) $p->receber_lembrete_evoluir;
        $this->foto_atual         = $p->foto ?? '';

        $this->comissao_personalizada = (bool) $p->comissao_personalizada;
        $this->percentual_comissao    = $p->percentual_comissao ?? '';

        $this->especialidadesSelecionadas = $p->especialidades->map(fn($e) => [
            'id' => $e->id, 'nome' => $e->nome, 'empresa' => $e->empresa ?? '',
        ])->toArray();

        $this->unidades = $p->unidades->map(fn($u) => [
            'id'      => $u->id,
            'unidade' => $u->unidade,
            'dias'    => $u->dias ?? self::diasPadrao(),
        ])->toArray();

        $this->perm_ver_somente_seus    = (bool) $p->perm_ver_somente_seus;
        $this->perm_fluxo_caixa         = (bool) $p->perm_fluxo_caixa;
        $this->perm_agendar_celular      = (bool) $p->perm_agendar_celular;
        $this->perm_editar_agenda        = (bool) $p->perm_editar_agenda;
        $this->perm_alterar_status       = (bool) $p->perm_alterar_status;
        $this->perm_acessar_cadastro     = (bool) $p->perm_acessar_cadastro;
        $this->perm_editar_recebimentos  = (bool) $p->perm_editar_recebimentos;
        $this->perm_remover_recebimentos = (bool) $p->perm_remover_recebimentos;
    }

    // ── CEP ──────────────────────────────────────────────────────────────────

    public function buscarCep(string $cepInput = ''): void
    {
        $cep = preg_replace('/\D/', '', $cepInput ?: $this->cep);
        $this->cep_erro    = '';
        $this->cep_loading = true;

        if (strlen($cep) !== 8) {
            $this->cep_loading = false;
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get("https://viacep.com.br/ws/{$cep}/json/");
            $data     = $response->json();

            if (isset($data['erro'])) {
                $this->cep_erro = 'CEP não encontrado.';
            } else {
                $this->endereco      = $data['logradouro'] ?? '';
                $this->bairro        = $data['bairro'] ?? '';
                $this->cidade        = $data['localidade'] ?? '';
                $this->uf            = $data['uf'] ?? '';
                $this->cep_encontrado = true;
            }
        } catch (\Throwable) {
            $this->cep_erro = 'Não foi possível consultar o CEP.';
        } finally {
            $this->cep_loading = false;
        }
    }

    public function limparCep(): void
    {
        $this->cep = $this->endereco = $this->bairro = $this->cidade = $this->uf = $this->numero = '';
        $this->cep_encontrado = false;
        $this->cep_erro       = '';
    }

    // ── Foto ─────────────────────────────────────────────────────────────────

    public function updatedFoto(): void
    {
        $this->validate(['foto' => 'image|max:2048']);
    }

    public function removerFoto(): void
    {
        if ($this->foto_atual) Storage::disk('public')->delete($this->foto_atual);
        $this->foto = null;
        $this->foto_atual = '';
    }

    // ── Especialidades ────────────────────────────────────────────────────────

    public function abrirModalEspecialidade(): void
    {
        $this->especialidade_nova_id = '';
        $this->modal_especialidade = true;
    }

    public function adicionarEspecialidade(): void
    {
        if (! $this->especialidade_nova_id) return;

        $esp = collect($this->especialidadesDisponiveis)->firstWhere('id', (int) $this->especialidade_nova_id);
        if (! $esp) return;

        $jaExiste = collect($this->especialidadesSelecionadas)->contains('id', $esp['id']);
        if (! $jaExiste) {
            $this->especialidadesSelecionadas[] = [
                'id'      => $esp['id'],
                'nome'    => $esp['nome'],
                'empresa' => $esp['empresa'] ?? '',
            ];
        }

        $this->modal_especialidade = false;
    }

    public function removerEspecialidade(int $index): void
    {
        array_splice($this->especialidadesSelecionadas, $index, 1);
    }

    // ── Unidades ──────────────────────────────────────────────────────────────

    public function abrirModalUnidade(?int $index = null): void
    {
        $this->unidade_edit_index = $index;
        if ($index !== null && isset($this->unidades[$index])) {
            $u = $this->unidades[$index];
            $this->unidade_nova = $u['unidade'];
            $this->unidade_dias = $u['dias'] ?? self::diasPadrao();
        } else {
            $this->unidade_nova = '';
            $this->unidade_dias = self::diasPadrao();
        }
        $this->modal_unidade = true;
    }

    public function clonarHorario(string $origem, string $destino): void
    {
        $src = $this->unidade_dias[$origem] ?? null;
        if (! $src) return;

        $diasUteis = ['seg','ter','qua','qui','sex'];
        $todos     = array_keys($this->unidade_dias);

        $alvos = match($destino) {
            'uteis' => array_filter($diasUteis, fn($d) => $d !== $origem),
            'todos' => array_filter($todos, fn($d) => $d !== $origem),
            default => [$destino],
        };

        foreach ($alvos as $alvo) {
            if (isset($this->unidade_dias[$alvo])) {
                $label = $this->unidade_dias[$alvo]['label'];
                $this->unidade_dias[$alvo] = [
                    'label'  => $label,
                    'ativo'  => true,
                    'inicio' => $src['inicio'],
                    'fim'    => $src['fim'],
                ];
            }
        }
    }

    public function confirmarUnidade(): void
    {
        $this->validate([
            'unidade_nova' => 'required|string',
        ], [], ['unidade_nova' => 'unidade']);

        $entry = [
            'id'      => null,
            'unidade' => $this->unidade_nova,
            'dias'    => $this->unidade_dias,
        ];

        if ($this->unidade_edit_index !== null) {
            $entry['id'] = $this->unidades[$this->unidade_edit_index]['id'] ?? null;
            $this->unidades[$this->unidade_edit_index] = $entry;
        } else {
            $this->unidades[] = $entry;
        }

        $this->modal_unidade = false;
        $this->dispatch('modal-unidade-salva', unidade: $this->unidade_nova);
    }

    public function removerUnidade(int $index): void
    {
        array_splice($this->unidades, $index, 1);
    }

    // ── Salvar ───────────────────────────────────────────────────────────────

    public function salvar(): void
    {
        try {
            $this->validate([
                'nome'  => 'required|string|max:255',
                'cpf'   => 'nullable|string|max:18',
                'email' => 'nullable|email|max:255',
                'foto'  => 'nullable|image|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('ir-para-aba', tab: 0);
            $this->dispatch('modal-erro', message: 'Verifique os campos obrigatórios antes de salvar.');
            throw $e;
        }

        // Validações de abas obrigatórias
        $primeiraAbaComErro = null;

        if (empty($this->especialidadesSelecionadas)) {
            $this->addError('especialidades', 'Adicione ao menos uma especialidade antes de salvar.');
            $primeiraAbaComErro ??= 2;
        }

        if (empty($this->unidades)) {
            $this->addError('unidades', 'Adicione ao menos uma unidade de atendimento antes de salvar.');
            $primeiraAbaComErro ??= 3;
        }

        $temPermissao = $this->perm_ver_somente_seus || $this->perm_fluxo_caixa
            || $this->perm_agendar_celular      || $this->perm_editar_agenda
            || $this->perm_alterar_status       || $this->perm_acessar_cadastro
            || $this->perm_editar_recebimentos  || $this->perm_remover_recebimentos;

        if (! $temPermissao) {
            $this->addError('permissoes', 'Ative ao menos uma permissão do app celular antes de salvar.');
            $primeiraAbaComErro ??= 4;
        }

        if ($primeiraAbaComErro !== null) {
            $this->dispatch('ir-para-aba', tab: $primeiraAbaComErro);
            return;
        }

        try {
            if ($this->foto) {
                if ($this->foto_atual) Storage::disk('public')->delete($this->foto_atual);
                $this->foto_atual = $this->foto->store('profissionais/fotos', 'public');
                $this->foto = null;
            }

            $dados = [
                'nome'               => $this->nome,
                'identificacao'      => $this->identificacao ?: Profissional::gerarCodigo(),
                'senha_celular'      => $this->senha_celular ?: null,
                'cpf'                => preg_replace('/\D/', '', $this->cpf) ?: null,
                'rg'                 => $this->rg ?: null,
                'conselho_profissional' => $this->conselho_profissional ?: null,
                'cep'                => preg_replace('/\D/', '', $this->cep) ?: null,
                'endereco'           => $this->endereco ?: null,
                'numero'             => $this->numero ?: null,
                'bairro'             => $this->bairro ?: null,
                'cidade'             => $this->cidade ?: null,
                'uf'                 => $this->uf ?: null,
                'email'              => $this->email ?: null,
                'telefone'           => $this->telefone ?: null,
                'celular1'           => $this->celular1 ?: null,
                'celular2'           => $this->celular2 ?: null,
                'data_nascimento'    => $this->data_nascimento ?: null,
                'ativo'              => $this->ativo,
                'ordem_agenda'       => $this->ordem_agenda,
                'pode_estender_horarios'   => $this->pode_estender_horarios,
                'horarios_flexiveis'       => $this->horarios_flexiveis,
                'receber_lembrete_evoluir' => $this->receber_lembrete_evoluir,
                'foto'               => $this->foto_atual ?: null,
                'comissao_personalizada' => $this->comissao_personalizada,
                'percentual_comissao'    => $this->percentual_comissao ?: null,
                'perm_ver_somente_seus'    => $this->perm_ver_somente_seus,
                'perm_fluxo_caixa'         => $this->perm_fluxo_caixa,
                'perm_agendar_celular'      => $this->perm_agendar_celular,
                'perm_editar_agenda'        => $this->perm_editar_agenda,
                'perm_alterar_status'       => $this->perm_alterar_status,
                'perm_acessar_cadastro'     => $this->perm_acessar_cadastro,
                'perm_editar_recebimentos'  => $this->perm_editar_recebimentos,
                'perm_remover_recebimentos' => $this->perm_remover_recebimentos,
            ];

            if ($this->profissionalId) {
                $profissional = Profissional::findOrFail($this->profissionalId);
                $profissional->update($dados);
                $msg = 'Profissional atualizado com sucesso!';
            } else {
                $profissional             = Profissional::create($dados);
                $this->profissionalId     = $profissional->id;
                $this->identificacao      = $profissional->identificacao;
                $msg = 'Profissional cadastrado com sucesso!';
            }

            // Sync especialidades
            $profissional->especialidades()->sync(
                collect($this->especialidadesSelecionadas)->pluck('id')->toArray()
            );

            // Sync unidades
            $profissional->unidades()->delete();
            foreach ($this->unidades as $u) {
                $profissional->unidades()->create([
                    'unidade' => $u['unidade'],
                    'dias'    => $u['dias'] ?? null,
                ]);
            }

            $this->dispatch('modal-sucesso', message: $msg);
        } catch (\Throwable $e) {
            \Log::error('CadastroProfissional::salvar — ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $this->dispatch('modal-erro', message: $e->getMessage());
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.profissionais.cadastro-profissional')
            ->layout('layouts.app', ['title' => 'Cadastro de Profissional']);
    }
}
