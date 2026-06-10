<?php

namespace App\Livewire\Clientes;

use App\Models\Convenio;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\TipoVinculo;
use App\Models\Unidade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CadastroCliente extends Component
{
    use WithFileUploads;

    // ── ID para modo edição ─────────────────────────────────────────────────
    public ?int $pacienteId = null;

    // Foto do paciente
    public $foto = null;
    public string $foto_atual = '';

    // Informações Gerais
    public string $codigo = '';
    public string $status_cliente = 'ativo';
    public string $nome = '';
    public string $cpf = '';
    public string $cnpj = '';
    public string $genero = '';
    public string $rg = '';
    public bool $ativo = true;
    public string $data_nascimento = '';
    public string $idade = '';
    public string $data_ativacao = '';
    public string $data_inativacao = '';
    public string $cep = '';
    public string $empresa = '';
    public string $endereco = '';
    public string $numero = '';
    public string $cidade = '';
    public string $uf = '';
    public string $complemento = '';
    public string $bairro = '';
    public string $telefone1 = '';
    public string $celular1 = '';
    public string $celular2 = '';
    public string $email = '';
    public string $tipo_vinculo_id = '';
    public string $profissional_responsavel_id = '';
    public string $convenio_id = '';
    public string $informacoes_iniciais = '';

    // WhatsApp e Mensagens
    public bool $enviar_whatsapp = false;
    public string $mensagens_por = '';
    public bool $whatsapp_ativo = false;
    public bool $sms_ativo = false;
    public bool $numero_internacional = false;

    // Situação Financeira
    public bool $inadimplente = false;
    public bool $tem_guia_finalizada = false;
    public bool $devendo_guia = false;

    // Opções/Checkboxes
    public bool $matricula_trancada = false;
    public bool $controle_matricula = false;
    public bool $financeiro_pendente = false;
    public bool $atencao_informacoes = false;

    // Rodapé
    public string $inicio_plano = '';
    public string $fim_plano = '';

    // CEP lookup state
    public bool   $cep_loading    = false;
    public bool   $cep_encontrado = false;
    public string $cep_erro       = '';

    // Dados auxiliares (para selects)
    public array $convenios        = [];
    public array $tiposVinculo     = [];
    public array $profissionais    = [];
    public array $unidades         = [];

    // ── Lifecycle ───────────────────────────────────────────────────────────

    public function mount(?int $pacienteId = null): void
    {
        $this->carregarDadosAuxiliares();

        if ($pacienteId) {
            $this->pacienteId = $pacienteId;
            $this->carregarPaciente($pacienteId);
        }
    }

    private function carregarDadosAuxiliares(): void
    {
        $this->convenios     = Convenio::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
        $this->tiposVinculo  = TipoVinculo::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
        $this->profissionais = Profissional::ativos()->orderBy('nome')->get(['id','nome'])->toArray();
        $this->unidades      = Unidade::ativas()->orderBy('nome')->get(['id','nome'])->toArray();
    }

    private function carregarPaciente(int $id): void
    {
        $p = Paciente::findOrFail($id);

        $this->codigo                      = $p->codigo ?? '';
        $this->status_cliente              = $p->status_cliente ?? 'ativo';
        $this->nome                        = $p->nome ?? '';
        $this->cpf                         = $p->cpf ?? '';
        $this->cnpj                        = $p->cnpj ?? '';
        $this->genero                      = $p->genero ?? '';
        $this->rg                          = $p->rg ?? '';
        $this->ativo                       = (bool) $p->ativo;
        $this->data_nascimento             = $p->data_nascimento ? $p->data_nascimento->format('Y-m-d') : '';
        $this->data_ativacao               = $p->data_ativacao ? $p->data_ativacao->format('Y-m-d') : '';
        $this->data_inativacao             = $p->data_inativacao ? $p->data_inativacao->format('Y-m-d') : '';
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
        $this->inicio_plano                = $p->inicio_plano ? $p->inicio_plano->format('Y-m-d') : ($p->data_ativacao ? $p->data_ativacao->format('Y-m-d') : '');
        $this->fim_plano                   = $p->fim_plano ? $p->fim_plano->format('Y-m-d') : '';
        $this->foto_atual                  = $p->foto ?? '';

        $this->calcularIdade();
    }

    // ── Hooks ────────────────────────────────────────────────────────────────

    public function updatedDataAtivacao(string $value): void
    {
        if (! $this->inicio_plano) {
            $this->inicio_plano = $value;
        }
    }

    // ── CEP ──────────────────────────────────────────────────────────────────

    public function buscarCep(string $cepInput = ''): void
    {
        $cep = preg_replace('/\D/', '', $cepInput ?: $this->cep);
        $this->cep            = $cep;
        $this->cep_erro       = '';
        $this->cep_encontrado = false;

        if (strlen($cep) !== 8) {
            return;
        }

        $this->cep_loading = true;

        try {
            $res = Http::timeout(6)->get("https://viacep.com.br/ws/{$cep}/json/");

            if (! $res->successful()) {
                $this->cep_erro = 'Erro ao consultar o CEP. Tente novamente.';
                return;
            }

            $data = $res->json();

            if (! empty($data['erro'])) {
                $this->cep_erro = 'CEP não encontrado.';
                return;
            }

            $this->endereco       = $data['logradouro'] ?? '';
            $this->bairro         = $data['bairro']     ?? '';
            $this->cidade         = $data['localidade'] ?? '';
            $this->uf             = $data['uf']         ?? '';
            $this->cep_encontrado = true;

        } catch (\Throwable $e) {
            $this->cep_erro = 'Não foi possível consultar o CEP.';
        } finally {
            $this->cep_loading = false;
        }
    }

    public function limparCep(): void
    {
        $this->cep            = '';
        $this->endereco       = '';
        $this->bairro         = '';
        $this->cidade         = '';
        $this->uf             = '';
        $this->cep_encontrado = false;
        $this->cep_erro       = '';
    }

    // ── Idade ────────────────────────────────────────────────────────────────

    public function updatedDataNascimento(): void
    {
        $this->calcularIdade();
    }

    public function calcularIdade(): void
    {
        if ($this->data_nascimento) {
            $this->idade = (string) \Carbon\Carbon::parse($this->data_nascimento)->age;
        } else {
            $this->idade = '';
        }
    }

    // ── Foto ─────────────────────────────────────────────────────────────────

    public function updatedFoto(): void
    {
        $this->validate(['foto' => 'image|max:2048']);
    }

    public function removerFoto(): void
    {
        if ($this->foto_atual) {
            Storage::disk('public')->delete($this->foto_atual);
        }
        $this->foto       = null;
        $this->foto_atual = '';
    }

    // ── Salvar ───────────────────────────────────────────────────────────────

    public function salvar(): void
    {
        try {
            $this->validate([
                'nome'                        => 'required|string|max:255',
                'cpf'                         => 'required|string|max:18',
                'rg'                          => 'required|string|max:20',
                'data_nascimento'             => 'required|date|before:today',
                'genero'                      => 'required|string|max:50',
                'data_ativacao'               => 'required|date',
                'empresa'                     => 'nullable|string|max:255',
                'cep'                         => 'required|string|max:9',
                'celular1'                    => 'required|string|max:20',
                'email'                       => 'required|email|max:255',
                'tipo_vinculo_id'             => 'required|exists:tipos_vinculo,id',
                'profissional_responsavel_id' => 'required|exists:profissionais,id',
                'convenio_id'                 => 'required|exists:convenios,id',
                'informacoes_iniciais'        => 'nullable|string|max:5000',
                'foto'                        => 'nullable|image|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('modal-erro', message: 'Verifique os campos obrigatórios antes de salvar.');
            throw $e; // mantém os erros inline nos campos
        }

        // Processa foto
        if ($this->foto) {
            if ($this->foto_atual) {
                Storage::disk('public')->delete($this->foto_atual);
            }
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

        try {
            if ($this->pacienteId) {
                $paciente = Paciente::findOrFail($this->pacienteId);
                $paciente->update($dados);
                $this->dispatch('modal-sucesso', message: 'Paciente atualizado com sucesso!');
            } else {
                $dados['codigo'] = Paciente::gerarCodigo();
                $paciente        = Paciente::create($dados);
                $this->pacienteId = $paciente->id;
                $this->codigo     = $paciente->codigo;
                $this->dispatch('modal-sucesso', message: 'Paciente cadastrado com sucesso!');
            }

            $this->dispatch('paciente-salvo', id: $paciente->id);
        } catch (\Throwable $e) {
            $this->dispatch('modal-erro', message: 'Erro ao salvar paciente. Tente novamente.');
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.clientes.cadastro-cliente')
            ->layout('layouts.app', ['title' => 'Cadastro de Paciente']);
    }
}
