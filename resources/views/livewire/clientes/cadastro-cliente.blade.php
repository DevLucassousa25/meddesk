<div
    x-data="{
        tab: 0,
        cnpj: false,
        msgType: 'whatsapp',
    }"
    class="font-['Inter',system-ui,sans-serif]"
>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 mb-5 text-xs text-slate-400">
        <a href="{{ route('pacientes.lista') }}" class="hover:text-blue-600 transition-colors">Lista de Pacientes</a>
        <x-lucide-chevron-right class="w-3 h-3" />
        <span class="text-slate-600 font-medium">Novo Paciente</span>
    </div>

    {{-- Patient header --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 sm:p-5 flex flex-wrap items-center gap-3 sm:gap-4 mb-4">
        {{-- Avatar --}}
        <div class="relative flex-shrink-0">
            <label for="foto-input-top" class="w-16 h-16 rounded-[14px] border-2 border-dashed border-blue-200 bg-blue-50 flex items-center justify-center cursor-pointer hover:border-blue-400 hover:bg-blue-100 transition-all overflow-hidden relative">
                @if ($foto)
                    <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover" />
                @elseif ($foto_atual)
                    <img src="{{ Storage::url($foto_atual) }}" class="w-full h-full object-cover" />
                @else
                    <x-lucide-user class="w-7 h-7 text-blue-400" />
                @endif

                {{-- Upload loading overlay --}}
                <div
                    wire:loading.flex wire:target="foto"
                    class="absolute inset-0 bg-white/80 items-center justify-center rounded-[12px]"
                >
                    <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </div>
            </label>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-blue-600 border-2 border-white flex items-center justify-center pointer-events-none">
                <x-lucide-plus class="w-2.5 h-2.5 text-white" />
            </div>
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            @if ($nome)
                <div class="text-lg font-bold text-slate-800 leading-tight truncate">{{ $nome }}</div>
            @else
                <div class="text-sm font-medium text-slate-300 italic">Novo Paciente</div>
            @endif
            <div class="flex flex-wrap gap-1.5 mt-1.5">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Pendente
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600">
                    Cadastro Novo
                </span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 ml-auto">

            {{-- More options dropdown --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button
                    type="button"
                    class="btn btn-secondary px-2.5"
                    style="height:38px;"
                    @click="open = !open"
                    :class="open ? 'bg-slate-200 border-slate-300' : ''"
                >
                    <x-lucide-more-horizontal class="w-4 h-4" />
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100 origin-top-right"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75 origin-top-right"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-1.5 w-52 bg-white rounded-xl border border-slate-200/80 shadow-[0_8px_24px_rgba(26,32,53,.12)] z-50 overflow-hidden py-1"
                >
                    {{-- Section: Cadastro --}}
                    <div class="px-3 py-1.5">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Cadastro</p>
                    </div>

                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-copy class="w-3.5 h-3.5 text-slate-400" />
                        Duplicar Cadastro
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-file-text class="w-3.5 h-3.5 text-slate-400" />
                        Exportar PDF
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-printer class="w-3.5 h-3.5 text-slate-400" />
                        Imprimir Ficha
                    </button>

                    <div class="h-px bg-slate-100 my-1"></div>

                    {{-- Section: Paciente --}}
                    <div class="px-3 py-1.5">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Paciente</p>
                    </div>

                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-history class="w-3.5 h-3.5 text-slate-400" />
                        Histórico de Alterações
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-send class="w-3.5 h-3.5 text-slate-400" />
                        Enviar Mensagem
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-calendar-plus class="w-3.5 h-3.5 text-slate-400" />
                        Novo Agendamento
                    </button>

                    <div class="h-px bg-slate-100 my-1"></div>

                    <button type="button" @click="open = false" class="dropdown-item text-amber-600 hover:bg-amber-50">
                        <x-lucide-lock class="w-3.5 h-3.5" />
                        Bloquear Paciente
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item text-red-500 hover:bg-red-50">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        Excluir Cadastro
                    </button>
                </div>
            </div>
            <button type="button" class="btn btn-primary" style="height:38px;">
                <x-lucide-plus class="w-4 h-4" />
                <span class="hidden sm:inline">Criar Agendamento</span>
            </button>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex bg-white border border-slate-100 rounded-t-xl border-b-0 overflow-x-auto scrollbar-none" style="-webkit-overflow-scrolling:touch;scrollbar-width:none;">
        @foreach(['Informações do Paciente', 'Histórico de Consultas', 'Próximo Tratamento'] as $i => $tlabel)
            <button
                type="button"
                class="px-4 py-3.5 text-[12.5px] font-medium border-b-2 transition-all whitespace-nowrap cursor-pointer"
                :class="tab === {{ $i }}
                    ? 'text-blue-600 font-semibold border-blue-600 bg-white'
                    : 'text-slate-400 border-transparent hover:text-slate-600'"
                @click="tab = {{ $i }}"
            >{{ $tlabel }}</button>
        @endforeach
    </div>

    {{-- ─── TAB 0 – Informações do Paciente ─── --}}
    <div x-show="tab === 0" x-cloak>
        <div class="grid grid-cols-1 xl:grid-cols-[1fr_260px] gap-3.5 items-start pt-3">

            {{-- ── LEFT COLUMN ── --}}
            <div class="space-y-3">

                {{-- 1. Identificação --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-blue-50">
                            <x-lucide-user class="w-3.5 h-3.5 text-blue-600" />
                        </div>
                        <div class="flex-1">
                            <div class="card-title">Identificação</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Dados cadastrais principais</div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Novo
                        </span>
                    </div>
                    <div class="card-body space-y-3">

                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_2fr] gap-3">
                            <div>
                                <label class="form-label">Código</label>
                                <input type="text" wire:model="codigo" placeholder="0000000" readonly class="form-input bg-slate-50 text-slate-400 cursor-default" />
                            </div>
                            <div>
                                <label class="form-label">Status Atual do Cliente</label>
                                <x-select-dots
                                    wire="status_cliente"
                                    placeholder="Selecione o status..."
                                    :options="[
                                        ['value' => 'Ativo',     'label' => 'Ativo',     'color' => '#22c55e'],
                                        ['value' => 'Inativo',   'label' => 'Inativo',   'color' => '#94a3b8'],
                                        ['value' => 'Bloqueado', 'label' => 'Bloqueado', 'color' => '#ef4444'],
                                        ['value' => 'Aguardando','label' => 'Aguardando','color' => '#f59e0b'],
                                    ]"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Nome Completo <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="text" wire:model.live="nome" placeholder="Nome completo do paciente" class="form-input @error('nome') is-invalid @enderror" />
                            @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="col-span-2">
                                <label class="form-label flex items-center gap-2">
                                    <span x-text="cnpj ? 'CNPJ' : 'CPF'"></span><span class="text-red-500">*</span>
                                    {{-- Mini CNPJ toggle chip --}}
                                    <button
                                        type="button"
                                        @click="cnpj = !cnpj"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md border text-[10px] font-bold uppercase tracking-wide transition-all duration-150 cursor-pointer"
                                        :class="cnpj
                                            ? 'bg-blue-600 border-blue-600 text-white'
                                            : 'bg-white border-slate-300 text-slate-400 hover:border-blue-400 hover:text-blue-500'"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full transition-colors"
                                            :class="cnpj ? 'bg-white' : 'bg-slate-300'"
                                        ></span>
                                        CNPJ
                                    </button>
                                </label>
                                <input
                                    type="text"
                                    :placeholder="cnpj ? '00.000.000/0000-00' : '000.000.000-00'"
                                    wire:model="cpf"
                                    class="form-input @error('cpf') is-invalid @enderror"
                                />
                                @error('cpf') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">RG <span class="text-red-500 ml-0.5">*</span></label>
                                <input type="text" wire:model="rg" placeholder="0000000000" class="form-input @error('rg') is-invalid @enderror" />
                                @error('rg') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Gênero <span class="text-red-500 ml-0.5">*</span></label>
                                <x-select-dots
                                    wire="genero"
                                    placeholder="Selecionar..."
                                    :invalid="$errors->has('genero')"
                                    :options="[
                                        ['value' => 'Masculino',          'label' => 'Masculino',          'color' => '#3b82f6'],
                                        ['value' => 'Feminino',           'label' => 'Feminino',           'color' => '#ec4899'],
                                        ['value' => 'Outro',              'label' => 'Outro',              'color' => '#2563eb'],
                                        ['value' => 'Prefiro não informar','label' => 'Prefiro não informar','color' => '#94a3b8'],
                                    ]"
                                />
                                @error('genero') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="form-label">Data de Nascimento <span class="text-red-500 ml-0.5">*</span></label>
                                <x-datepicker wire="data_nascimento" :invalid="$errors->has('data_nascimento')" />
                                @error('data_nascimento') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Idade</label>
                                <input type="number" wire:model="idade" placeholder="—" class="form-input" />
                            </div>
                            <div>
                                <label class="form-label">Data de Ativação <span class="text-red-500 ml-0.5">*</span></label>
                                <x-datepicker wire="data_ativacao" :invalid="$errors->has('data_ativacao')" />
                                @error('data_ativacao') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Data de Inativação</label>
                                <x-datepicker wire="data_inativacao" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_2fr] gap-3">
                            <div>
                                <label class="form-label">Ativo</label>
                                <x-select-dots
                                    wire="ativo"
                                    :options="[
                                        ['value' => '1', 'label' => 'Sim', 'color' => '#22c55e'],
                                        ['value' => '0', 'label' => 'Não', 'color' => '#ef4444'],
                                    ]"
                                />
                            </div>
                            <div>
                                <label class="form-label">Empresa</label>
                                @if(empty($unidades))
                                    <input type="text" wire:model="empresa" placeholder="Nome da empresa/unidade" class="form-input @error('empresa') is-invalid @enderror" />
                                @else
                                    @php
                                        $colors = ['#2563eb','#0d9488','#7c3aed','#b45309','#dc2626','#059669'];
                                        $empOpts = collect($unidades)->values()->map(fn($u,$i) => [
                                            'value' => $u['nome'],
                                            'label' => $u['nome'],
                                            'color' => $colors[$i % count($colors)],
                                        ])->toArray();
                                    @endphp
                                    <x-select-dots
                                        wire="empresa"
                                        placeholder="Selecionar unidade..."
                                        :invalid="$errors->has('empresa')"
                                        :options="$empOpts"
                                    />
                                @endif
                                @error('empresa') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 2. Endereço --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-blue-50">
                            <x-lucide-map-pin class="w-3.5 h-3.5 text-blue-600" />
                        </div>
                        <div class="flex-1">
                            <div class="card-title">Endereço</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Localização e correspondência</div>
                        </div>
                        {{-- Found badge + clear button --}}
                        @if ($cep_encontrado)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-green-50 text-green-700 border border-green-200">
                                    <x-lucide-check-circle class="w-3 h-3" />
                                    CEP encontrado
                                </span>
                                <button type="button" wire:click="limparCep" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-semibold text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all border border-slate-200 hover:border-red-200" title="Limpar endereço">
                                    <x-lucide-x class="w-3 h-3" />
                                    Limpar
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="card-body space-y-3">

                        {{-- Row 1: CEP + Endereço + Número --}}
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                            {{-- CEP — Alpine mask (display: 00000-000, backend: raw digits) --}}
                            <div
                                x-data="{
                                    raw: '{{ preg_replace('/\D/', '', $cep) }}',
                                    get fmt() {
                                        return this.raw.length > 5
                                            ? this.raw.slice(0,5) + '-' + this.raw.slice(5)
                                            : this.raw;
                                    },
                                    onInput(e) {
                                        this.raw = e.target.value.replace(/\D/g, '').slice(0, 8);
                                        e.target.value = this.fmt;
                                    },
                                    onBlur() {
                                        if (this.raw.length === 8) {
                                            $wire.buscarCep(this.raw);
                                        } else if (this.raw.length > 0) {
                                            $wire.set('cep', this.raw);
                                        }
                                    },
                                    init() {
                                        // Keep in sync when limparCep() resets the wire property
                                        $wire.$watch('cep', v => {
                                            this.raw = String(v || '').replace(/\D/g, '');
                                        });
                                    }
                                }"
                            >
                                <label class="form-label flex items-center gap-1.5">
                                    CEP <span class="text-red-500 ml-0.5">*</span>
                                    <span
                                        wire:loading wire:target="buscarCep"
                                        class="text-[10px] text-blue-500 font-semibold"
                                    >buscando...</span>
                                </label>

                                <div class="relative">
                                    <input
                                        type="text"
                                        :value="fmt"
                                        @input="onInput($event)"
                                        @blur="onBlur()"
                                        placeholder="00000-000"
                                        maxlength="9"
                                        inputmode="numeric"
                                        class="form-input pr-8 transition-all @error('cep') is-invalid @enderror"
                                        @if ($cep_encontrado) readonly @endif
                                        style="{{ $cep_erro ? 'border-color:#fca5a5;' : '' }}"
                                    />

                                    {{-- Status icon --}}
                                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center">
                                        {{-- Spinner while fetching --}}
                                        <svg
                                            wire:loading wire:target="buscarCep"
                                            class="w-3.5 h-3.5 text-blue-500 animate-spin"
                                            fill="none" viewBox="0 0 24 24"
                                        >
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                        {{-- Static icons (hidden during fetch) --}}
                                        @if ($cep_encontrado)
                                            <x-lucide-check
                                                wire:loading.remove wire:target="buscarCep"
                                                class="w-3.5 h-3.5 text-slate-400"
                                            />
                                        @elseif ($cep_erro)
                                            <x-lucide-alert-circle
                                                wire:loading.remove wire:target="buscarCep"
                                                class="w-3.5 h-3.5 text-red-400"
                                            />
                                        @endif
                                    </div>
                                </div>

                                @if ($cep_erro)
                                    <p class="mt-1 text-[10.5px] font-medium text-red-500 flex items-center gap-1">
                                        <x-lucide-alert-circle class="w-3 h-3 flex-shrink-0" />
                                        {{ $cep_erro }}
                                    </p>
                                @endif
                            </div>

                            {{-- Endereço --}}
                            <div class="col-span-2">
                                <label class="form-label">Endereço</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        wire:model="endereco"
                                        placeholder="Rua, Avenida, Logradouro..."
                                        class="form-input transition-all{{ ($cep_encontrado && $endereco) ? ' pr-8' : '' }}"
                                        @if ($cep_encontrado && $endereco) readonly style="cursor:default;" @endif
                                    />
                                    @if ($cep_encontrado && $endereco)
                                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <x-lucide-lock class="w-3 h-3 text-slate-400" />
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Número - always editable --}}
                            <div>
                                <label class="form-label">Número</label>
                                <input type="text" wire:model="numero" placeholder="Nº" class="form-input" />
                            </div>
                        </div>

                        {{-- Row 2: Bairro + Cidade + UF + Complemento --}}
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                            {{-- Bairro --}}
                            <div>
                                <label class="form-label">Bairro</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        wire:model="bairro"
                                        placeholder="Bairro"
                                        class="form-input transition-all{{ ($cep_encontrado && $bairro) ? ' pr-8' : '' }}"
                                        @if ($cep_encontrado && $bairro) readonly style="cursor:default;" @endif
                                    />
                                    @if ($cep_encontrado && $bairro)
                                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <x-lucide-lock class="w-3 h-3 text-slate-400" />
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Cidade --}}
                            <div>
                                <label class="form-label">Cidade</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        wire:model="cidade"
                                        placeholder="Cidade..."
                                        class="form-input transition-all{{ ($cep_encontrado && $cidade) ? ' pr-8' : '' }}"
                                        @if ($cep_encontrado && $cidade) readonly style="cursor:default;" @endif
                                    />
                                    @if ($cep_encontrado && $cidade)
                                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <x-lucide-lock class="w-3 h-3 text-slate-400" />
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- UF --}}
                            <div>
                                <label class="form-label">UF</label>
                                @if ($cep_encontrado && $uf)
                                    <div class="relative">
                                        <input
                                            type="text"
                                            wire:model="uf"
                                            readonly
                                            class="form-input pr-8 transition-all"
                                            style="cursor:default;"
                                        />
                                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <x-lucide-lock class="w-3 h-3 text-slate-400" />
                                        </div>
                                    </div>
                                @else
                                    <x-select
                                        wire="uf"
                                        :options="collect(['AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO'])->map(fn($e) => ['value' => $e, 'label' => $e])->toArray()"
                                        placeholder="UF"
                                    />
                                @endif
                            </div>

                            {{-- Complemento - always editable --}}
                            <div>
                                <label class="form-label">Complemento</label>
                                <input type="text" wire:model="complemento" placeholder="Apto, Bloco..." class="form-input" />
                            </div>
                        </div>

                        {{-- CEP not found hint --}}
                        @if (! $cep_encontrado && ! $cep_erro && ! $cep_loading)
                            <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                <x-lucide-info class="w-3 h-3 flex-shrink-0" />
                                Digite o CEP para preencher o endereço automaticamente via ViaCEP.
                            </p>
                        @endif

                    </div>
                </div>

                {{-- 3. Contato --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-teal-50">
                            <x-lucide-phone class="w-3.5 h-3.5 text-teal-600" />
                        </div>
                        <div>
                            <div class="card-title">Contato</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Telefones, celulares e e-mail</div>
                        </div>
                    </div>
                    <div class="card-body space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="form-label">Telefone 1</label>
                                <div class="flex gap-1.5">
                                    <input type="text" wire:model="telefone1" placeholder="(00) 0000-0000" class="form-input" />
                                    <button type="button" class="btn btn-success flex-shrink-0 px-2.5" title="Enviar WhatsApp">
                                        <x-lucide-send class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Celular 1 (WA) <span class="text-red-500 ml-0.5">*</span></label>
                                <input type="text" wire:model="celular1" placeholder="(00) 90000-0000" class="form-input @error('celular1') is-invalid @enderror" />
                                @error('celular1') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Celular 2</label>
                                <input type="text" wire:model="celular2" placeholder="(00) 90000-0000" class="form-input" />
                            </div>
                        </div>

                        <div>
                            <label class="form-label">E-mail <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="email" wire:model="email" placeholder="paciente@email.com" class="form-input @error('email') is-invalid @enderror" />
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-lg p-3">
                            <div class="form-label mb-2">Mensagens Automáticas</div>
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="inline-flex bg-slate-200/60 rounded-lg p-0.5 gap-0.5">
                                    <button
                                        type="button"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all cursor-pointer"
                                        :class="msgType === 'whatsapp' ? 'bg-white text-blue-600 font-semibold shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                        @click="msgType = 'whatsapp'"
                                    >
                                        <x-lucide-smartphone class="w-3 h-3" />
                                        WhatsApp
                                    </button>
                                    <button
                                        type="button"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all cursor-pointer"
                                        :class="msgType === 'sms' ? 'bg-white text-blue-600 font-semibold shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                        @click="msgType = 'sms'"
                                    >
                                        <x-lucide-message-square class="w-3 h-3" />
                                        SMS
                                    </button>
                                </div>
                                <x-check-item wire="numero_internacional" icon="globe" label="Número Internacional" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Informações Clínicas --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-green-50">
                            <x-lucide-stethoscope class="w-3.5 h-3.5 text-green-600" />
                        </div>
                        <div>
                            <div class="card-title">Informações Clínicas</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Vínculo, profissional e convênio</div>
                        </div>
                    </div>
                    <div class="card-body space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Tipo de Vínculo Principal <span class="text-red-500 ml-0.5">*</span></label>
                                @php
                                    $colors = ['#3b82f6','#22c55e','#2563eb','#f59e0b','#ec4899','#0d9488','#8b5cf6'];
                                    $tiposOpts = collect($tiposVinculo)->values()->map(fn($t,$i) => [
                                        'value' => (string)$t['id'],
                                        'label' => $t['nome'],
                                        'color' => $colors[$i % count($colors)],
                                    ])->toArray();
                                @endphp
                                <x-select-dots
                                    wire="tipo_vinculo_id"
                                    placeholder="Selecionar vínculo..."
                                    :invalid="$errors->has('tipo_vinculo_id')"
                                    :options="$tiposOpts"
                                />
                                @error('tipo_vinculo_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Profissional Responsável <span class="text-red-500 ml-0.5">*</span></label>
                                @php
                                    $profOpts = collect($profissionais)->values()->map(fn($p,$i) => [
                                        'value' => (string)$p['id'],
                                        'label' => $p['nome'],
                                        'color' => $colors[$i % count($colors)],
                                    ])->toArray();
                                @endphp
                                <x-select-dots
                                    wire="profissional_responsavel_id"
                                    placeholder="Selecionar profissional..."
                                    :invalid="$errors->has('profissional_responsavel_id')"
                                    :options="$profOpts"
                                />
                                @error('profissional_responsavel_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Convênio / Forma de Pagamento <span class="text-red-500 ml-0.5">*</span></label>
                            @php
                                $convOpts = collect($convenios)->values()->map(fn($c,$i) => [
                                    'value' => (string)$c['id'],
                                    'label' => $c['nome'],
                                    'color' => $colors[$i % count($colors)],
                                ])->toArray();
                            @endphp
                            <x-select-dots
                                wire="convenio_id"
                                placeholder="Selecionar convênio ou forma de pagamento..."
                                :invalid="$errors->has('convenio_id')"
                                :options="$convOpts"
                            />
                            @error('convenio_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Informações Iniciais</label>
                            <textarea
                                wire:model="informacoes_iniciais"
                                placeholder="Observações, histórico inicial, informações relevantes sobre o paciente..."
                                class="form-input resize-y min-h-[80px] leading-relaxed @error('informacoes_iniciais') is-invalid @enderror"
                                rows="3"
                            ></textarea>
                            @error('informacoes_iniciais') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- 5. Status Financeiro --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-amber-50">
                            <x-lucide-wallet class="w-3.5 h-3.5 text-amber-600" />
                        </div>
                        <div>
                            <div class="card-title">Status Financeiro</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Inadimplência e controle de guias</div>
                        </div>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="form-label">Inadimplente</label>
                                <x-select-dots
                                    wire="inadimplente"
                                    :options="[
                                        ['value' => '0', 'label' => 'Não', 'color' => '#22c55e'],
                                        ['value' => '1', 'label' => 'Sim', 'color' => '#ef4444'],
                                    ]"
                                />
                            </div>
                            <div>
                                <label class="form-label">Tem Guia Finalizada</label>
                                <x-select-dots
                                    wire="tem_guia_finalizada"
                                    :options="[
                                        ['value' => '0', 'label' => 'Não', 'color' => '#94a3b8'],
                                        ['value' => '1', 'label' => 'Sim', 'color' => '#22c55e'],
                                    ]"
                                />
                            </div>
                            <div>
                                <label class="form-label">Está Devendo Guia</label>
                                <x-select-dots
                                    wire="devendo_guia"
                                    :options="[
                                        ['value' => '0', 'label' => 'Não', 'color' => '#22c55e'],
                                        ['value' => '1', 'label' => 'Sim', 'color' => '#ef4444'],
                                    ]"
                                />
                            </div>
                        </div>

                        <div class="h-px bg-slate-100"></div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <x-check-item wire="matricula_trancada"  icon="lock"            label="Matrícula Trancada"  />
                            <x-check-item wire="controle_matricula"  icon="graduation-cap"  label="Controle Matrícula"  />
                            <x-check-item wire="financeiro_pendente" icon="alert-triangle"  label="Financeiro Pendente" />
                        </div>
                    </div>
                </div>

            </div>{{-- /left --}}

            {{-- ── RIGHT COLUMN ── --}}
            <div class="xl:sticky xl:top-4 space-y-3 order-first xl:order-last">

                {{-- Photo --}}
                <div class="card">
                    <div class="card-body">
                        <label for="foto-input" class="relative w-full aspect-square border-2 border-dashed border-blue-200 rounded-xl bg-blue-50/50 flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all gap-2 group overflow-hidden">
                            @if ($foto)
                                <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover" />
                            @elseif ($foto_atual)
                                <img src="{{ Storage::url($foto_atual) }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <x-lucide-upload class="w-5 h-5 text-blue-500" />
                                </div>
                                <div class="text-xs font-semibold text-blue-600">Adicionar foto</div>
                                <div class="text-[10.5px] text-slate-400">JPG ou PNG · Máx 5MB</div>
                            @endif

                            {{-- Upload loading overlay --}}
                            <div
                                wire:loading.flex wire:target="foto"
                                class="absolute inset-0 bg-white/85 backdrop-blur-[2px] flex-col items-center justify-center gap-2 rounded-xl"
                            >
                                <svg class="w-8 h-8 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                    <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <span class="text-[11px] font-semibold text-blue-600">Enviando...</span>
                            </div>
                        </label>
                        <input type="file" id="foto-input" wire:model="foto" accept="image/*" class="hidden" />
                        @error('foto') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Plano Atual --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-blue-50">
                            <x-lucide-calendar-range class="w-3.5 h-3.5 text-blue-600" />
                        </div>
                        <div class="card-title">Plano Atual</div>
                    </div>
                    <div class="card-body space-y-3">
                        <div>
                            <label class="form-label">Início do Plano</label>
                            <x-datepicker wire="inicio_plano" />
                        </div>
                        <div>
                            <label class="form-label">Fim do Plano</label>
                            <x-datepicker wire="fim_plano" />
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100/80 rounded-lg p-3 border border-blue-100">
                            <div class="form-label mb-1">Status do Plano</div>
                            @if ($inicio_plano && $fim_plano)
                                @php
                                    $pInicio  = \Carbon\Carbon::parse($inicio_plano);
                                    $pFim     = \Carbon\Carbon::parse($fim_plano);
                                    $pHoje    = now();
                                    $pTotal   = max($pInicio->diffInDays($pFim), 1);
                                    $pDecorr  = min($pInicio->diffInDays($pHoje), $pTotal);
                                    $pPct     = round(($pDecorr / $pTotal) * 100);
                                    $pRestam  = max((int) $pHoje->diffInDays($pFim, false), 0);
                                @endphp
                                <div class="text-[15px] font-bold text-slate-800">{{ $pRestam }} dias restantes</div>
                                <div class="h-1.5 bg-blue-200 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-600 to-blue-500 rounded-full" style="width: {{ $pPct }}%"></div>
                                </div>
                                <div class="text-[10.5px] text-slate-500 mt-1.5">{{ $pPct }}% do plano utilizado</div>
                            @else
                                <div class="text-[15px] font-bold text-slate-800">A definir</div>
                                <div class="h-1.5 bg-blue-200 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-600 to-blue-500 rounded-full" style="width:0%"></div>
                                </div>
                                <div class="text-[10.5px] text-slate-500 mt-1.5">Preencha as datas acima</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Ações Rápidas --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-slate-100">
                            <x-lucide-check-circle class="w-3.5 h-3.5 text-slate-500" />
                        </div>
                        <div class="card-title">Ações Rápidas</div>
                    </div>
                    <div class="card-body space-y-1.5">
                        @foreach([
                            ['icon' => 'pencil',         'label' => 'Contratos & Assinaturas'],
                            ['icon' => 'tag',            'label' => 'Patologias ou Tags'],
                            ['icon' => 'graduation-cap', 'label' => 'Controle de Matrícula'],
                            ['icon' => 'calendar-range', 'label' => 'Ver Agenda Semanal'],
                            ['icon' => 'bar-chart-2',    'label' => 'Rel. de Atendimentos'],
                            ['icon' => 'receipt',        'label' => 'Notas Fiscais'],
                        ] as $action)
                        <button type="button" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-slate-100 bg-white hover:border-blue-200 hover:bg-blue-50/50 hover:text-blue-600 text-slate-500 transition-all text-left cursor-pointer group">
                            <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-blue-100 flex items-center justify-center flex-shrink-0 transition-colors">
                                <x-dynamic-component :component="'lucide-' . $action['icon']" class="w-3.5 h-3.5" />
                            </div>
                            <span class="text-[12.5px] font-medium flex-1">{{ $action['label'] }}</span>
                            <x-lucide-chevron-right class="w-3 h-3 text-slate-300 group-hover:text-blue-400" />
                        </button>
                        @endforeach
                    </div>
                </div>

            </div>{{-- /right --}}

        </div>{{-- /grid --}}

        {{-- Footer --}}
        <div class="flex flex-wrap justify-end gap-2 pt-2 pb-2">
            <button type="button" class="btn btn-secondary">Cancelar</button>
            <button type="button" class="btn" style="background:#fff;color:#2563eb;border:1.5px solid #bfdbfe;font-weight:600;padding:8px 18px;font-size:13px;">
                <x-lucide-save class="w-3.5 h-3.5" />
                Rascunho
            </button>
            <button type="button" wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                <svg wire:loading wire:target="salvar" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <x-lucide-check wire:loading.remove wire:target="salvar" class="w-3.5 h-3.5" />
                <span wire:loading wire:target="salvar">Salvando...</span>
                <span wire:loading.remove wire:target="salvar">Salvar Paciente</span>
            </button>
        </div>

    </div>{{-- /tab 0 --}}

    {{-- ─── TAB 1 – Histórico ─── --}}
    <div x-show="tab === 1" x-cloak class="pt-3">
        <div class="card">
            <div class="card-body py-14 text-center text-slate-400">
                <x-lucide-book-open class="w-9 h-9 mx-auto mb-3 opacity-40" />
                <div class="text-[13px] font-semibold">Nenhum histórico disponível</div>
                <div class="text-xs mt-1">O histórico aparecerá após o cadastro</div>
            </div>
        </div>
    </div>

    {{-- ─── TAB 2 – Próximo Tratamento ─── --}}
    <div x-show="tab === 2" x-cloak class="pt-3">
        <div class="card">
            <div class="card-body py-14 text-center text-slate-400">
                <x-lucide-stethoscope class="w-9 h-9 mx-auto mb-3 opacity-40" />
                <div class="text-[13px] font-semibold">Nenhum tratamento agendado</div>
                <div class="text-xs mt-1">Crie um agendamento para este paciente</div>
            </div>
        </div>
    </div>

    {{-- Modal de Erro --}}
    <div
        x-data="{ open: false, message: '' }"
        x-show="open"
        x-cloak
        @modal-erro.window="open = true; message = $event.detail.message"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="fixed inset-0 bg-black/30" @click="open = false"></div>
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center gap-5"
        >
            {{-- Ícone --}}
            <div class="w-16 h-16 rounded-full bg-red-500 flex items-center justify-center shadow-lg shadow-red-200">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            {{-- Texto --}}
            <div class="space-y-1.5">
                <p class="text-[17px] font-bold text-slate-800">Ocorreu um erro!</p>
                <p class="text-sm text-slate-500 leading-relaxed" x-text="message"></p>
            </div>
            {{-- Botões --}}
            <div class="flex gap-3 w-full mt-1">
                <button @click="open = false" class="flex-1 h-11 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Fechar</button>
            </div>
        </div>
    </div>

    {{-- Modal de Sucesso --}}
    <div
        x-data="{ open: false, message: '' }"
        x-show="open"
        x-cloak
        @modal-sucesso.window="open = true; message = $event.detail.message"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="fixed inset-0 bg-black/30" @click="open = false"></div>
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center gap-5"
        >
            {{-- Ícone --}}
            <div class="w-16 h-16 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-200">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            {{-- Texto --}}
            <div class="space-y-1.5">
                <p class="text-[17px] font-bold text-slate-800">Salvo com sucesso!</p>
                <p class="text-sm text-slate-500 leading-relaxed" x-text="message"></p>
            </div>
            {{-- Botões --}}
            <div class="flex gap-3 w-full mt-1">
                <button @click="open = false" class="flex-1 h-11 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Fechar</button>
                <button @click="open = false" class="flex-1 h-11 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-colors shadow-sm shadow-emerald-200">OK</button>
            </div>
        </div>
    </div>

</div>
