<div
    x-data="{ tab: 0 }"
    @ir-para-aba.window="tab = $event.detail.tab"
    class="font-['Inter',system-ui,sans-serif]"
>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 mb-5 text-xs text-slate-400">
        <a href="{{ route('profissionais.lista') }}" class="hover:text-blue-600 transition-colors">Equipe</a>
        <x-lucide-chevron-right class="w-3 h-3" />
        <span class="text-slate-600 font-medium">{{ $profissionalId ? $nome : 'Novo Profissional' }}</span>
    </div>

    {{-- Header card --}}
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
                <div wire:loading.flex wire:target="foto" class="absolute inset-0 bg-white/80 items-center justify-center rounded-[12px]">
                    <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </div>
            </label>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-blue-600 border-2 border-white flex items-center justify-center pointer-events-none">
                <x-lucide-plus class="w-2.5 h-2.5 text-white" />
            </div>
            <input id="foto-input-top" type="file" wire:model="foto" accept="image/*" class="hidden" />
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            @if ($nome)
                <div class="text-lg font-bold text-slate-800 leading-tight truncate">{{ $nome }}</div>
            @else
                <div class="text-sm font-medium text-slate-300 italic">Novo Profissional</div>
            @endif
            <div class="flex flex-wrap gap-1.5 mt-1.5">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $ativo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $ativo ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $ativo ? 'Ativo' : 'Inativo' }}
                </span>
                @if($conselho_profissional)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600">
                        {{ $conselho_profissional }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex gap-2 ml-auto">
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="btn btn-secondary px-2.5" style="height:38px;" @click="open = !open">
                    <x-lucide-more-horizontal class="w-4 h-4" />
                </button>
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-100 origin-top-right"
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-1.5 w-52 bg-white rounded-xl border border-slate-200/80 shadow-[0_8px_24px_rgba(26,32,53,.12)] z-50 overflow-hidden py-1">
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-layout-dashboard class="w-3.5 h-3.5 text-slate-400" /> Painel de Recebimentos
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-calendar-range class="w-3.5 h-3.5 text-slate-400" /> Disponibilidade de Horários
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-settings class="w-3.5 h-3.5 text-slate-400" /> Configurar Horários
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-smartphone class="w-3.5 h-3.5 text-slate-400" /> Liberar no App
                    </button>
                    <button type="button" @click="open = false" class="dropdown-item">
                        <x-lucide-file-text class="w-3.5 h-3.5 text-slate-400" /> Dados para o TISS
                    </button>
                    <div class="h-px bg-slate-100 my-1"></div>
                    <button type="button" @click="open = false" class="dropdown-item text-red-500 hover:bg-red-50">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" /> Remover Cadastro
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex bg-white border border-slate-100 rounded-t-xl border-b-0 overflow-x-auto scrollbar-none" style="-webkit-overflow-scrolling:touch;scrollbar-width:none;">
        @php
            $tabErros = [
                0 => $errors->has('nome') || $errors->has('cpf') || $errors->has('email'),
                2 => $errors->has('especialidades'),
                3 => $errors->has('unidades'),
                4 => $errors->has('permissoes'),
            ];
        @endphp
        @foreach(['Informações Gerais','Comissão','Especialidades','Unidades','Permissões App'] as $i => $tlabel)
            <button type="button"
                class="relative px-4 py-3.5 text-[12.5px] font-medium border-b-2 transition-all whitespace-nowrap cursor-pointer"
                :class="tab === {{ $i }} ? 'text-blue-600 font-semibold border-blue-600 bg-white' : '{{ ($tabErros[$i] ?? false) ? 'text-red-500 border-red-400' : 'text-slate-400 border-transparent hover:text-slate-600' }}'"
                @click="tab = {{ $i }}"
            >
                {{ $tlabel }}
                @if($tabErros[$i] ?? false)
                    <span class="absolute top-2 right-1.5 w-1.5 h-1.5 rounded-full bg-red-500"></span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB 0 — Informações Gerais
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 0" x-cloak>
        <div class="grid grid-cols-1 xl:grid-cols-[1fr_260px] gap-3.5 items-start pt-3">

            {{-- Coluna esquerda --}}
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
                    </div>
                    <div class="card-body space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="form-label">Nome Completo <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="nome" placeholder="Nome completo" class="form-input @error('nome') is-invalid @enderror" />
                                @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Identificação</label>
                                <input type="text" wire:model="identificacao"
                                    placeholder="{{ $profissionalId ? '—' : 'Gerado ao salvar' }}"
                                    readonly
                                    class="form-input bg-slate-50 text-slate-400 cursor-default" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="form-label">Senha Acesso Celular</label>
                                <input type="password" wire:model="senha_celular" placeholder="••••••" class="form-input" autocomplete="new-password" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Conselho Profissional</label>
                                <input type="text" wire:model="conselho_profissional" placeholder="Ex: CRM 12345, CRP 67890..." class="form-input" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Documentos --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-violet-50">
                            <x-lucide-id-card class="w-3.5 h-3.5 text-violet-600" />
                        </div>
                        <div class="card-title">Documentos</div>
                    </div>
                    <div class="card-body space-y-3">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="form-label">CPF</label>
                                <input type="text" wire:model="cpf" placeholder="000.000.000-00" class="form-input @error('cpf') is-invalid @enderror" />
                                @error('cpf') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">RG</label>
                                <input type="text" wire:model="rg" placeholder="0000000000" class="form-input" />
                            </div>
                            <div>
                                <label class="form-label">Data de Nascimento</label>
                                <x-datepicker wire="data_nascimento" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Endereço --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-emerald-50">
                            <x-lucide-map-pin class="w-3.5 h-3.5 text-emerald-600" />
                        </div>
                        <div class="card-title">Endereço</div>
                    </div>
                    <div class="card-body space-y-3"
                        x-data="{
                            raw: '{{ preg_replace('/\D/', '', $cep) }}',
                            fmt(v){ v=v.replace(/\D/g,'').slice(0,8); return v.length>5?v.slice(0,5)+'-'+v.slice(5):v; },
                            onInput(e){ this.raw=e.target.value.replace(/\D/g,''); e.target.value=this.fmt(this.raw); $wire.set('cep',this.raw); if(this.raw.length===8) $wire.buscarCep(this.raw); }
                        }"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr_80px] gap-3">
                            <div>
                                <label class="form-label">CEP</label>
                                <div class="relative">
                                    <input type="text" :value="fmt(raw)" @input="onInput($event)" placeholder="00000-000" maxlength="9" inputmode="numeric"
                                        class="form-input pr-8 transition-all @error('cep') is-invalid @enderror"
                                        @if($cep_encontrado) readonly @endif />
                                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <svg wire:loading wire:target="buscarCep" class="w-3.5 h-3.5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                        @if($cep_encontrado) <x-lucide-check wire:loading.remove wire:target="buscarCep" class="w-3.5 h-3.5 text-green-500" /> @endif
                                        @if($cep_erro) <x-lucide-alert-circle wire:loading.remove wire:target="buscarCep" class="w-3.5 h-3.5 text-red-400" /> @endif
                                    </div>
                                </div>
                                @if($cep_encontrado)
                                    <button type="button" wire:click="limparCep" class="text-[11px] text-slate-400 hover:text-red-500 mt-1 transition-colors">Limpar</button>
                                @endif
                                @if($cep_erro) <p class="text-[11px] text-red-500 mt-1">{{ $cep_erro }}</p> @endif
                            </div>
                            <div>
                                <label class="form-label">Endereço</label>
                                <input type="text" wire:model="endereco" placeholder="Rua, Avenida..." class="form-input" @if($cep_encontrado && $endereco) readonly @endif />
                            </div>
                            <div>
                                <label class="form-label">Nº</label>
                                <input type="text" wire:model="numero" placeholder="Nº" class="form-input" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="col-span-2">
                                <label class="form-label">Bairro</label>
                                <input type="text" wire:model="bairro" placeholder="Bairro" class="form-input" @if($cep_encontrado && $bairro) readonly @endif />
                            </div>
                            <div>
                                <label class="form-label">Cidade</label>
                                <input type="text" wire:model="cidade" placeholder="Cidade" class="form-input" @if($cep_encontrado && $cidade) readonly @endif />
                            </div>
                            <div>
                                <label class="form-label">UF</label>
                                @if($cep_encontrado && $uf)
                                    <input type="text" wire:model="uf" readonly class="form-input" />
                                @else
                                    <select wire:model="uf" class="form-input">
                                        <option value="">UF</option>
                                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $estado)
                                            <option value="{{ $estado }}">{{ $estado }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Contato --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-sky-50">
                            <x-lucide-phone class="w-3.5 h-3.5 text-sky-600" />
                        </div>
                        <div class="card-title">Contato</div>
                    </div>
                    <div class="card-body space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="form-label">E-mail</label>
                                <input type="email" wire:model="email" placeholder="profissional@email.com" class="form-input @error('email') is-invalid @enderror" />
                                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Telefone</label>
                                <input type="text" wire:model="telefone" placeholder="(00) 0000-0000" class="form-input" />
                            </div>
                            <div>
                                <label class="form-label">Celular 1</label>
                                <input type="text" wire:model="celular1" placeholder="(00) 90000-0000" class="form-input" />
                            </div>
                            <div>
                                <label class="form-label">Celular 2</label>
                                <input type="text" wire:model="celular2" placeholder="(00) 90000-0000" class="form-input" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna direita --}}
            <div class="space-y-3">

                {{-- Foto --}}
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
                                <div class="text-[10.5px] text-slate-400">JPG ou PNG · Máx 2MB</div>
                            @endif
                        </label>
                        <input type="file" id="foto-input" wire:model="foto" accept="image/*" class="hidden" />
                        @if($foto || $foto_atual)
                            <button wire:click="removerFoto" type="button" class="text-[11px] text-red-400 hover:text-red-600 mt-1 transition-colors">Remover foto</button>
                        @endif
                    </div>
                </div>

                {{-- Situação --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-slate-50">
                            <x-lucide-toggle-right class="w-3.5 h-3.5 text-slate-600" />
                        </div>
                        <div class="card-title">Situação</div>
                    </div>
                    <div class="card-body">
                        <x-check-item wire="ativo" icon="circle-check" label="Profissional Ativo" />
                    </div>
                </div>

                {{-- Agenda --}}
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon bg-blue-50">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-blue-600" />
                        </div>
                        <div class="card-title">Agenda</div>
                    </div>
                    <div class="card-body space-y-3">
                        <div>
                            <label class="form-label">Ordem na Agenda</label>
                            <input type="number" wire:model="ordem_agenda" min="1" max="99" class="form-input w-24" />
                        </div>
                        <div class="space-y-2">
                            <x-check-item wire="receber_lembrete_evoluir" icon="bell" label="Lembrete de evoluir" />
                            <x-check-item wire="pode_estender_horarios" icon="clock" label="Pode estender horários" />
                            <x-check-item wire="horarios_flexiveis" icon="sliders" label="Horários flexíveis" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB 1 — Comissão
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 1" x-cloak class="pt-3">
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon bg-emerald-50">
                    <x-lucide-percent class="w-3.5 h-3.5 text-emerald-600" />
                </div>
                <div class="flex-1">
                    <div class="card-title">Configurações de Comissão</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Percentual sobre atendimentos e recebimentos</div>
                </div>
            </div>
            <div class="card-body space-y-4">
                <x-check-item wire="comissao_personalizada" icon="toggle-right" label="Ativar comissão personalizada para este profissional" />
                <div x-show="$wire.comissao_personalizada"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display:none">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        {{-- Input percentual --}}
                        <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-3">
                            <div>
                                <label class="form-label">Percentual de comissão</label>
                                <div class="relative mt-1">
                                    <input
                                        type="number"
                                        wire:model="percentual_comissao"
                                        min="0" max="100" step="0.01"
                                        placeholder="0,00"
                                        class="form-input pr-10"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">%</span>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                                    <span>0%</span>
                                    <span x-text="($wire.percentual_comissao || 0) + '%'">0%</span>
                                    <span>100%</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                                         :style="'width:' + Math.min($wire.percentual_comissao || 0, 100) + '%'">
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400">Aplicado sobre recebimentos vinculados a este profissional.</p>
                        </div>

                        {{-- Simulação --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 flex flex-col justify-between gap-3">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Simulação</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Recebimento</span>
                                    <span class="text-sm font-semibold text-slate-700">R$ 1.000,00</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Percentual</span>
                                    <span class="text-sm font-semibold text-slate-700"
                                          x-text="($wire.percentual_comissao || 0) + '%'">0%</span>
                                </div>
                                <div class="h-px bg-slate-200"></div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-600">Comissão gerada</span>
                                    <span class="text-base font-bold text-emerald-600"
                                          x-text="'R$ ' + (($wire.percentual_comissao || 0) * 10).toFixed(2).replace('.',',')">
                                        R$ 0,00
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB 2 — Especialidades
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 2" x-cloak class="pt-3">
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon bg-blue-50">
                    <x-lucide-stethoscope class="w-3.5 h-3.5 text-blue-600" />
                </div>
                <div class="flex-1">
                    <div class="card-title">Especialidades</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ count($especialidadesSelecionadas) }} especialidade(s) vinculada(s)</div>
                </div>
                <button type="button" wire:click="abrirModalEspecialidade" class="btn btn-primary text-xs gap-1.5">
                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar
                </button>
            </div>
            <div class="card-body">
                @error('especialidades')
                    <div class="flex items-center gap-2 px-3 py-2.5 mb-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium">
                        <x-lucide-alert-circle class="w-4 h-4 flex-shrink-0" />
                        {{ $message }}
                    </div>
                @enderror
                @if(empty($especialidadesSelecionadas))
                    <div class="py-10 text-center text-slate-400">
                        <x-lucide-stethoscope class="w-8 h-8 mx-auto mb-2 opacity-30" />
                        <p class="text-sm">Nenhuma especialidade vinculada</p>
                        <button type="button" wire:click="abrirModalEspecialidade" class="btn btn-primary text-xs mt-3 gap-1">
                            <x-lucide-plus class="w-3 h-3" /> Adicionar especialidade
                        </button>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach($especialidadesSelecionadas as $idx => $esp)
                        <div class="flex items-center justify-between py-3 px-1 group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <x-lucide-stethoscope class="w-3.5 h-3.5 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ $esp['nome'] }}</p>
                                    @if($esp['empresa']) <p class="text-xs text-slate-400">{{ $esp['empresa'] }}</p> @endif
                                </div>
                            </div>
                            <button type="button" wire:click="removerEspecialidade({{ $idx }})"
                                class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB 3 — Unidades
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 3" x-cloak class="pt-3">
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon bg-amber-50">
                    <x-lucide-building-2 class="w-3.5 h-3.5 text-amber-600" />
                </div>
                <div class="flex-1">
                    <div class="card-title">Unidades de Atendimento</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Horários por unidade</div>
                </div>
                <button type="button" wire:click="abrirModalUnidade" class="btn btn-primary text-xs gap-1.5">
                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar
                </button>
            </div>
            <div class="card-body">
                @error('unidades')
                    <div class="flex items-center gap-2 px-3 py-2.5 mb-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium">
                        <x-lucide-alert-circle class="w-4 h-4 flex-shrink-0" />
                        {{ $message }}
                    </div>
                @enderror
                @if(empty($unidades))
                    <div class="py-10 text-center text-slate-400">
                        <x-lucide-building-2 class="w-8 h-8 mx-auto mb-2 opacity-30" />
                        <p class="text-sm">Nenhuma unidade vinculada</p>
                        <button type="button" wire:click="abrirModalUnidade" class="btn btn-primary text-xs mt-3 gap-1">
                            <x-lucide-plus class="w-3 h-3" /> Adicionar unidade
                        </button>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/60">
                                <th class="text-left py-2.5 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Unidade</th>
                                <th class="text-left py-2.5 px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Dias Ativos</th>
                                <th class="w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($unidades as $idx => $u)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="py-3 px-3 font-medium text-slate-700">{{ $u['unidade'] }}</td>
                                <td class="py-3 px-3">
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $diasAtivos = collect($u['dias'] ?? [])->filter(fn($d) => $d['ativo'] ?? false);
                                        @endphp
                                        @forelse($diasAtivos as $k => $d)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[11px] font-semibold">
                                                {{ $d['label'] }}
                                                @if($d['inicio'] && $d['fim'])
                                                    <span class="text-blue-400 font-normal">{{ $d['inicio'] }}–{{ $d['fim'] }}</span>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-300">Nenhum dia configurado</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex gap-1 justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" wire:click="abrirModalUnidade({{ $idx }})"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-colors">
                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button type="button" wire:click="removerUnidade({{ $idx }})"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Resumo semanal --}}
                    @php
                        $totalMinutosSemana = 0;
                        $resumoPorDia = ['seg'=>0,'ter'=>0,'qua'=>0,'qui'=>0,'sex'=>0,'sab'=>0,'dom'=>0];

                        foreach($unidades as $u) {
                            foreach(($u['dias'] ?? []) as $key => $d) {
                                if(($d['ativo'] ?? false) && $d['inicio'] && $d['fim']) {
                                    [$hI, $mI] = explode(':', $d['inicio']);
                                    [$hF, $mF] = explode(':', $d['fim']);
                                    $mins = ((int)$hF * 60 + (int)$mF) - ((int)$hI * 60 + (int)$mI);
                                    if($mins > 0) {
                                        $totalMinutosSemana += $mins;
                                        $resumoPorDia[$key] = ($resumoPorDia[$key] ?? 0) + $mins;
                                    }
                                }
                            }
                        }

                        $totalH = intdiv($totalMinutosSemana, 60);
                        $totalM = $totalMinutosSemana % 60;
                        $diasComHora = array_filter($resumoPorDia, fn($m) => $m > 0);
                    @endphp

                    @if($totalMinutosSemana > 0)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            {{-- Total --}}
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                    <x-lucide-clock class="w-4 h-4 text-emerald-600" />
                                </div>
                                <div>
                                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total semanal</div>
                                    <div class="text-lg font-bold text-slate-800 leading-tight">
                                        {{ $totalH }}h{{ $totalM > 0 ? str_pad($totalM,2,'0',STR_PAD_LEFT).'min' : '' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Dias da semana --}}
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $labsDia = ['seg'=>'Seg','ter'=>'Ter','qua'=>'Qua','qui'=>'Qui','sex'=>'Sex','sab'=>'Sáb','dom'=>'Dom'];
                                @endphp
                                @foreach($labsDia as $key => $lab)
                                    @if(($resumoPorDia[$key] ?? 0) > 0)
                                        @php
                                            $h = intdiv($resumoPorDia[$key], 60);
                                            $m = $resumoPorDia[$key] % 60;
                                        @endphp
                                        <div class="flex flex-col items-center px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-100 min-w-[48px]">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $lab }}</span>
                                            <span class="text-xs font-semibold text-slate-700 mt-0.5">{{ $h }}h{{ $m > 0 ? str_pad($m,2,'0',STR_PAD_LEFT) : '' }}</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-100 min-w-[48px] opacity-30">
                                            <span class="text-[10px] font-bold text-slate-300 uppercase">{{ $lab }}</span>
                                            <span class="text-xs text-slate-300 mt-0.5">—</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB 4 — Permissões App
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 4" x-cloak class="pt-3">
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon bg-slate-50">
                    <x-lucide-smartphone class="w-3.5 h-3.5 text-slate-600" />
                </div>
                <div class="flex-1">
                    <div class="card-title">Permissões App Celular</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">O que este profissional pode acessar pelo app</div>
                </div>
            </div>
            <div class="card-body">
                @error('permissoes')
                    <div class="flex items-center gap-2 px-3 py-2.5 mb-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium">
                        <x-lucide-alert-circle class="w-4 h-4 flex-shrink-0" />
                        {{ $message }}
                    </div>
                @enderror
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <x-check-item wire="perm_ver_somente_seus"    icon="eye"           label="Visualizar somente seus atendimentos" />
                <x-check-item wire="perm_fluxo_caixa"         icon="wallet"        label="Pode visualizar fluxo de caixa" />
                <x-check-item wire="perm_agendar_celular"      icon="calendar-plus" label="Pode agendar pelo celular" />
                <x-check-item wire="perm_editar_agenda"        icon="calendar-check" label="Pode editar atendimentos/agenda" />
                <x-check-item wire="perm_alterar_status"       icon="refresh-cw"   label="Pode alterar status do atendimento" />
                <x-check-item wire="perm_acessar_cadastro"     icon="user"         label="Pode acessar cadastro do paciente" />
                <x-check-item wire="perm_editar_recebimentos"  icon="edit"         label="Pode editar recebimentos" />
                <x-check-item wire="perm_remover_recebimentos" icon="trash-2"      label="Pode remover recebimentos" />
            </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="flex flex-wrap justify-end gap-2 pt-3 pb-2">
        <a href="{{ route('profissionais.lista') }}" class="btn btn-secondary">Cancelar</a>
        <button type="button" wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
            <svg wire:loading wire:target="salvar" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <x-lucide-check wire:loading.remove wire:target="salvar" class="w-3.5 h-3.5" />
            <span wire:loading wire:target="salvar">Salvando...</span>
            <span wire:loading.remove wire:target="salvar">Salvar Profissional</span>
        </button>
    </div>

    {{-- ══ Modal Especialidade ══ --}}
    @if($modal_especialidade)
    @php
        $optsEspModal = array_merge(
            [['value' => '', 'label' => 'Selecionar especialidade...']],
            collect($especialidadesDisponiveis)->map(fn($e) => [
                'value' => (string) $e['id'],
                'label' => $e['nome'],
                'sub'   => $e['empresa'] ?: null,
            ])->toArray()
        );
    @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="$set('modal_especialidade', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <x-lucide-award class="w-4 h-4 text-white" />
                </div>
                <h3 class="text-base font-bold text-slate-800 flex-1">Adicionar Especialidade</h3>
                <button type="button" wire:click="$set('modal_especialidade', false)"
                    class="w-7 h-7 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-5 py-4 space-y-1">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Especialidade</label>
                <x-select
                    wire="especialidade_nova_id"
                    :options="$optsEspModal"
                    placeholder="Selecionar especialidade..."
                    :searchable="count($especialidadesDisponiveis) > 5"
                    icon="lucide-award"
                />
                @if(empty($especialidadesDisponiveis))
                    <p class="text-xs text-slate-400 mt-2">Nenhuma especialidade disponível. <a href="{{ route('config.especialidades') }}" class="text-blue-500 hover:underline">Cadastrar</a></p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button type="button" wire:click="$set('modal_especialidade', false)"
                    class="flex-1 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="adicionarEspecialidade"
                    class="flex-1 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
                    @if(!$especialidade_nova_id) disabled @endif>
                    <x-lucide-plus class="w-4 h-4" />
                    Adicionar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ Modal Unidade ══ --}}
    @if($modal_unidade)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="$set('modal_unidade', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <x-lucide-building-2 class="w-4 h-4 text-amber-500" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $unidade_edit_index !== null ? 'Editar' : 'Adicionar' }} Unidade
                    </h3>
                </div>
                <button type="button" wire:click="$set('modal_unidade', false)"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-5">

                {{-- Seleção da unidade --}}
                <div>
                    <label class="form-label">Unidade <span class="text-red-500">*</span></label>
                    @if(empty($unidadesDisponiveis))
                        <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-dashed border-slate-200 bg-slate-50">
                            <x-lucide-building-2 class="w-4 h-4 text-slate-300 flex-shrink-0" />
                            <span class="text-xs text-slate-400">Nenhuma unidade cadastrada. </span>
                            <a href="{{ route('config.unidades') }}" target="_blank"
                               class="text-xs text-blue-500 hover:text-blue-700 font-semibold transition-colors">
                                Cadastrar →
                            </a>
                        </div>
                    @else
                        @php
                            $optsUnidade = collect($unidadesDisponiveis)->map(fn($u) => [
                                'value' => $u['nome'],
                                'label' => $u['nome'],
                            ])->toArray();
                        @endphp
                        <x-select
                            wire="unidade_nova"
                            :options="$optsUnidade"
                            placeholder="Selecionar unidade..."
                            :searchable="count($unidadesDisponiveis) > 5"
                            icon="lucide-building-2"
                        />
                    @endif
                </div>

                {{-- Grade semanal --}}
                <div>
                    <label class="form-label mb-2">Horários por Dia da Semana</label>
                    <div class="space-y-2">
                        @foreach($unidade_dias as $key => $dia)
                        <div class="flex items-center gap-3 p-2.5 rounded-xl border transition-colors
                            {{ $dia['ativo'] ? 'border-blue-200 bg-blue-50/50' : 'border-slate-100 bg-slate-50' }}">

                            {{-- Toggle dia --}}
                            <button type="button"
                                wire:click="$set('unidade_dias.{{ $key }}.ativo', {{ $dia['ativo'] ? 'false' : 'true' }})"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0
                                    {{ $dia['ativo'] ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-400 hover:border-blue-300' }}">
                                @if($dia['ativo'])
                                    <x-lucide-check class="w-3.5 h-3.5" />
                                @else
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                @endif
                            </button>

                            {{-- Label dia --}}
                            <span class="w-8 text-xs font-bold {{ $dia['ativo'] ? 'text-blue-700' : 'text-slate-400' }} flex-shrink-0">
                                {{ $dia['label'] }}
                            </span>

                            {{-- Horários --}}
                            @if($dia['ativo'])
                            <div class="flex items-center gap-2 flex-1">
                                <input type="time"
                                    wire:model="unidade_dias.{{ $key }}.inicio"
                                    class="form-input text-xs h-8 flex-1"
                                    style="padding: 4px 8px; font-size: 12px;"
                                />
                                <span class="text-slate-400 text-xs flex-shrink-0">até</span>
                                <input type="time"
                                    wire:model="unidade_dias.{{ $key }}.fim"
                                    class="form-input text-xs h-8 flex-1"
                                    style="padding: 4px 8px; font-size: 12px;"
                                />
                            </div>

                            {{-- Botão clonar --}}
                            <div class="relative flex-shrink-0" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open"
                                    title="Clonar horário"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-blue-100 transition-colors">
                                    <x-lucide-copy class="w-3.5 h-3.5" />
                                </button>
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute right-0 top-8 z-50 bg-white border border-slate-200 rounded-xl shadow-lg py-1 w-44">
                                    <p class="px-3 py-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Copiar para</p>
                                    <button type="button" @click="$wire.clonarHorario('{{ $key }}', 'uteis'); open = false"
                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-2">
                                        <x-lucide-calendar-days class="w-3.5 h-3.5" /> Dias úteis (Seg–Sex)
                                    </button>
                                    <button type="button" @click="$wire.clonarHorario('{{ $key }}', 'todos'); open = false"
                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-2">
                                        <x-lucide-calendar class="w-3.5 h-3.5" /> Todos os dias
                                    </button>
                                    <div class="h-px bg-slate-100 my-1"></div>
                                    @foreach($unidade_dias as $dk => $dd)
                                        @if($dk !== $key)
                                        <button type="button" @click="$wire.clonarHorario('{{ $key }}', '{{ $dk }}'); open = false"
                                            class="w-full text-left px-3 py-2 text-xs text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                            Apenas {{ $dd['label'] }}
                                        </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @else
                            <span class="text-xs text-slate-300 italic flex-1">Não atende</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">Clique no dia para ativar/desativar o atendimento.</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button type="button" wire:click="$set('modal_unidade', false)"
                    class="flex-1 h-10 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="confirmarUnidade" wire:loading.attr="disabled" wire:target="confirmarUnidade"
                    class="flex-1 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="confirmarUnidade" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <x-lucide-check wire:loading.remove wire:target="confirmarUnidade" class="w-4 h-4" />
                    <span wire:loading wire:target="confirmarUnidade">Salvando...</span>
                    <span wire:loading.remove wire:target="confirmarUnidade">Concluir</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Unidade Salva --}}
    <div x-data="{ open: false, unidade: '' }" x-show="open" x-cloak
         @modal-unidade-salva.window="open = true; unidade = $event.detail.unidade; setTimeout(() => open = false, 2500)"
         class="fixed inset-0 z-50 flex items-end justify-center pb-8 pointer-events-none">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="bg-slate-800 text-white rounded-2xl px-5 py-3.5 flex items-center gap-3 shadow-2xl pointer-events-auto">
            <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold">Horário configurado!</p>
                <p class="text-xs text-slate-400" x-text="unidade"></p>
            </div>
        </div>
    </div>

    {{-- Modal Erro --}}
    <div x-data="{ open: false, message: '' }" x-show="open" x-cloak @modal-erro.window="open = true; message = $event.detail.message" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/30" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center gap-5">
            <div class="w-16 h-16 rounded-full bg-red-500 flex items-center justify-center shadow-lg shadow-red-200">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div class="space-y-1.5">
                <p class="text-[17px] font-bold text-slate-800">Ocorreu um erro!</p>
                <p class="text-sm text-slate-500 leading-relaxed" x-text="message"></p>
            </div>
            <button @click="open = false" class="w-full h-11 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Fechar</button>
        </div>
    </div>

    {{-- Modal Sucesso --}}
    <div x-data="{ open: false, message: '' }" x-show="open" x-cloak @modal-sucesso.window="open = true; message = $event.detail.message" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/30" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 flex flex-col items-center text-center gap-5">
            <div class="w-16 h-16 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-200">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="space-y-1.5">
                <p class="text-[17px] font-bold text-slate-800">Salvo com sucesso!</p>
                <p class="text-sm text-slate-500 leading-relaxed" x-text="message"></p>
            </div>
            <div class="flex gap-3 w-full mt-1">
                <button @click="open = false" class="flex-1 h-11 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">Fechar</button>
                <button @click="open = false" class="flex-1 h-11 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-colors">OK</button>
            </div>
        </div>
    </div>

</div>
