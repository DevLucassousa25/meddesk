<div
    class="font-['Inter',system-ui,sans-serif]"
    x-data
    @modal-success.window="$store.modal.confirm({
        type: 'success',
        title: $event.detail.title,
        message: $event.detail.message,
        confirmText: 'OK',
        cancelText: null,
    })"
>

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Unidades</h1>
            <p class="text-sm text-slate-400 mt-0.5">Gerencie as unidades e filiais da clínica</p>
        </div>
        <button type="button" wire:click="abrirCadastro" class="btn btn-primary gap-2 h-10 px-5 text-sm rounded-xl shadow-sm shadow-blue-200">
            <x-lucide-plus class="w-4 h-4" />
            Nova Unidade
        </button>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <x-lucide-building-2 class="w-4 h-4 text-blue-500" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Total</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ $unidades->count() }}</div>
            <div class="text-xs text-slate-400 mt-0.5">unidades cadastradas</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-500" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Ativas</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ $unidades->where('ativo', true)->count() }}</div>
            <div class="text-xs text-slate-400 mt-0.5">em operação</div>
        </div>
    </div>

    {{-- Barra de busca --}}
    <div class="bg-white border border-slate-100 rounded-xl mb-4">
        <div class="flex items-center gap-2.5 px-4 h-11">
            <x-lucide-search class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
            <input
                wire:model.live.debounce.300ms="busca"
                type="text"
                placeholder="Buscar por nome ou cidade..."
                class="bg-transparent border-none outline-none text-sm text-slate-700 placeholder-slate-400 w-full"
            />
            @if($busca)
                <button wire:click="$set('busca', '')" class="text-slate-300 hover:text-slate-500">
                    <x-lucide-x class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-white border border-slate-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Unidade</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Cidade</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Endereço</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-3 pr-4 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($unidades as $unidade)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                    {{ $unidade->isPrincipal() ? 'bg-blue-100' : 'bg-slate-100' }}">
                                    @if($unidade->isPrincipal())
                                        <x-lucide-building-2 class="w-3.5 h-3.5 text-blue-600" />
                                    @else
                                        <x-lucide-map-pin class="w-3.5 h-3.5 text-slate-400" />
                                    @endif
                                </div>
                                <span class="font-medium text-slate-700">{{ $unidade->nome }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($unidade->isPrincipal())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-semibold border border-blue-100">
                                    <x-lucide-star class="w-3 h-3" /> Principal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[11px] font-semibold">
                                    <x-lucide-git-branch class="w-3 h-3" /> Filial
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500 hidden md:table-cell text-xs">
                            {{ $unidade->cidade ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-400 hidden lg:table-cell text-xs truncate max-w-[220px]">
                            {{ $unidade->endereco ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" wire:click="toggleAtivo({{ $unidade->id }})">
                                @if($unidade->ativo)
                                    <span class="badge badge-green">Ativa</span>
                                @else
                                    <span class="badge badge-gray">Inativa</span>
                                @endif
                            </button>
                        </td>
                        <td class="pr-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" wire:click="editar({{ $unidade->id }})" title="Editar"
                                    class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors">
                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                </button>
                                <button type="button"
                                    wire:click="excluir({{ $unidade->id }})"
                                    wire:confirm="Deseja excluir a unidade '{{ $unidade->nome }}'?"
                                    title="Excluir"
                                    class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-1">
                                    <x-lucide-building-2 class="w-5 h-5 text-slate-300" />
                                </div>
                                <p class="text-sm font-medium text-slate-500">Nenhuma unidade cadastrada</p>
                                <p class="text-xs text-slate-400">Cadastre a primeira unidade da clínica.</p>
                                <button type="button" wire:click="abrirCadastro" class="btn btn-primary text-xs mt-2 gap-1.5">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Nova Unidade
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($unidades->count() > 0)
        <div class="px-5 py-3 border-t border-slate-100">
            <p class="text-xs text-slate-400">{{ $unidades->count() }} unidade(s) cadastrada(s)</p>
        </div>
        @endif
    </div>

    {{-- ══ Modal Cadastro / Edição ══ --}}
    @if($modalAberto)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="fecharModal"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <x-lucide-building-2 class="w-4 h-4 text-blue-500" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $editandoId ? 'Editar Unidade' : 'Nova Unidade' }}
                    </h3>
                </div>
                <button wire:click="fecharModal" type="button"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="form-label">Nome da Unidade <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nome" placeholder="Ex: Unidade Centro, Filial Norte..." class="form-input @error('nome') is-invalid @enderror" />
                    @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tipo --}}
                <div>
                    <label class="form-label">Tipo</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                            {{ $tipo === 'principal' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300' }}">
                            <input wire:model.live="tipo" type="radio" value="principal" class="sr-only" />
                            <div class="w-8 h-8 rounded-lg {{ $tipo === 'principal' ? 'bg-blue-100' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                                <x-lucide-building-2 class="w-4 h-4 {{ $tipo === 'principal' ? 'text-blue-600' : 'text-slate-400' }}" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold {{ $tipo === 'principal' ? 'text-blue-700' : 'text-slate-700' }}">Principal</p>
                                <p class="text-[11px] text-slate-400">Sede central</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                            {{ $tipo === 'filial' ? 'border-slate-400 bg-slate-50' : 'border-slate-200 hover:border-slate-300' }}">
                            <input wire:model.live="tipo" type="radio" value="filial" class="sr-only" />
                            <div class="w-8 h-8 rounded-lg {{ $tipo === 'filial' ? 'bg-slate-200' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                                <x-lucide-git-branch class="w-4 h-4 {{ $tipo === 'filial' ? 'text-slate-600' : 'text-slate-400' }}" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold {{ $tipo === 'filial' ? 'text-slate-700' : 'text-slate-600' }}">Filial</p>
                                <p class="text-[11px] text-slate-400">Unidade secundária</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Cidade</label>
                        <input type="text" wire:model="cidade" placeholder="São Paulo" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Estado</label>
                        <x-select
                            wire="uf"
                            :options="collect(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'])->map(fn($e) => ['value' => $e, 'label' => $e])->toArray()"
                            placeholder="UF"
                        />
                    </div>
                </div>

                <div>
                    <label class="form-label">Endereço</label>
                    <input type="text" wire:model="endereco" placeholder="Rua, número, bairro..." class="form-input" />
                </div>

                <x-check-item wire="ativo" icon="circle-check" label="Unidade ativa" />
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button type="button" wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                <button type="button" wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                    <svg wire:loading wire:target="salvar" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <x-lucide-check wire:loading.remove wire:target="salvar" class="w-3.5 h-3.5" />
                    <span wire:loading wire:target="salvar">Salvando...</span>
                    <span wire:loading.remove wire:target="salvar">{{ $editandoId ? 'Salvar Alterações' : 'Criar Unidade' }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif

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
