<div class="font-['Inter',system-ui,sans-serif]">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Tabela de Preços</h1>
            <p class="text-sm text-slate-400 mt-0.5">Gerencie os valores de consultas, exames e atendimentos</p>
        </div>
        <button type="button" wire:click="abrirCadastro" class="btn btn-primary gap-2 h-10 px-5 text-sm rounded-xl shadow-sm shadow-blue-200">
            <x-lucide-plus class="w-4 h-4" />
            Novo Item
        </button>
    </div>

    {{-- Cards de totais por tipo --}}
    <div class="grid grid-cols-3 gap-3 mb-5">
        @php
            $tiposConfig = [
                'consulta'    => ['label' => 'Consultas',    'icon' => 'stethoscope',  'bg' => 'bg-blue-50',    'text' => 'text-blue-500',   'badge' => 'badge-blue'],
                'exame'       => ['label' => 'Exames',       'icon' => 'microscope',   'bg' => 'bg-violet-50',  'text' => 'text-violet-500', 'badge' => 'badge-blue'],
                'atendimento' => ['label' => 'Atendimentos', 'icon' => 'heart-pulse',  'bg' => 'bg-emerald-50', 'text' => 'text-emerald-500','badge' => 'badge-green'],
            ];
        @endphp
        @foreach($tiposConfig as $tipoKey => $cfg)
        <button type="button" wire:click="$set('filtroTipo', '{{ $filtroTipo === $tipoKey ? '' : $tipoKey }}')"
            class="bg-white border rounded-xl px-4 py-3 text-left transition-all hover:shadow-sm {{ $filtroTipo === $tipoKey ? 'border-blue-300 shadow-sm shadow-blue-100' : 'border-slate-100' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg {{ $cfg['bg'] }} flex items-center justify-center flex-shrink-0">
                    @if($tipoKey === 'consulta')
                        <x-lucide-stethoscope class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                    @elseif($tipoKey === 'exame')
                        <x-lucide-flask-conical class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                    @else
                        <x-lucide-heart-pulse class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                    @endif
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider leading-none mb-0.5">{{ $cfg['label'] }}</p>
                    <p class="text-xl font-bold text-slate-800 leading-none">{{ $totais[$tipoKey] }}</p>
                </div>
            </div>
        </button>
        @endforeach
    </div>

    {{-- Barra de busca + filtro --}}
    <div class="flex flex-wrap gap-2 mb-4">
        <div class="bg-white border border-slate-100 rounded-xl flex-1 min-w-[200px]">
            <div class="flex items-center gap-2.5 px-4 h-11">
                <x-lucide-search class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                <input
                    wire:model.live.debounce.300ms="busca"
                    type="text"
                    placeholder="Buscar por nome ou código..."
                    class="bg-transparent border-none outline-none text-sm text-slate-700 placeholder-slate-400 w-full"
                />
                @if($busca)
                    <button wire:click="$set('busca', '')" class="text-slate-300 hover:text-slate-500">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                @endif
            </div>
        </div>

        @if($filtroTipo)
        <button type="button" wire:click="$set('filtroTipo', '')"
            class="h-11 px-4 bg-blue-50 text-blue-600 text-sm font-semibold rounded-xl border border-blue-200 flex items-center gap-2 hover:bg-blue-100 transition-colors">
            <x-lucide-x class="w-3.5 h-3.5" />
            {{ $tiposConfig[$filtroTipo]['label'] }}
        </button>
        @endif
    </div>

    {{-- Tabela --}}
    <div class="bg-white border border-slate-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nome / Código</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tipo</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Especialidade</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Duração</th>
                        <th class="text-right px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Valor</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-3 pr-4 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($itens as $item)
                    @php
                        $cfg = $tiposConfig[$item->tipo];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $cfg['bg'] }} flex items-center justify-center flex-shrink-0">
                                    @if($item->tipo === 'consulta')
                                        <x-lucide-stethoscope class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                    @elseif($item->tipo === 'exame')
                                        <x-lucide-flask-conical class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                    @else
                                        <x-lucide-heart-pulse class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                    @endif
                                </div>
                                <div>
                                    <span class="font-medium text-slate-700 block">{{ $item->nome }}</span>
                                    @if($item->codigo)
                                        <span class="text-[11px] text-slate-400">{{ $item->codigo }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($item->tipo === 'consulta')
                                <span class="badge badge-blue">Consulta</span>
                            @elseif($item->tipo === 'exame')
                                <span class="badge" style="background:#f3e8ff;color:#7c3aed;">Exame</span>
                            @else
                                <span class="badge badge-green">Atendimento</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs">
                            <div class="text-slate-500">{{ $item->especialidade?->nome ?? '—' }}</div>
                            @if(!empty($item->unidade_ids))
                                @php $nomes = \App\Models\Unidade::whereIn('id', $item->unidade_ids)->pluck('nome'); @endphp
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($nomes as $n)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-medium">
                                            <x-lucide-building-2 class="w-2.5 h-2.5" /> {{ $n }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 hidden lg:table-cell text-xs">
                            @if($item->duracao_minutos)
                                <div class="flex items-center gap-1">
                                    <x-lucide-clock class="w-3 h-3" />
                                    {{ $item->duracao_minutos }} min
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-slate-800">
                                R$ {{ number_format((float) $item->valor, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" wire:click="toggleAtivo({{ $item->id }})" class="cursor-pointer">
                                @if($item->ativo)
                                    <span class="badge badge-green">Ativo</span>
                                @else
                                    <span class="badge badge-gray">Inativo</span>
                                @endif
                            </button>
                        </td>
                        <td class="pr-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" wire:click="editar({{ $item->id }})" title="Editar"
                                    class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors">
                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                </button>
                                <button type="button"
                                    wire:click="excluir({{ $item->id }})"
                                    wire:confirm="Deseja excluir '{{ $item->nome }}'?"
                                    title="Excluir"
                                    class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-1">
                                    <x-lucide-tag class="w-5 h-5 text-slate-300" />
                                </div>
                                <p class="text-sm font-medium text-slate-500">Nenhum item cadastrado</p>
                                <p class="text-xs text-slate-400">Cadastre consultas, exames ou atendimentos com seus valores.</p>
                                <button type="button" wire:click="abrirCadastro" class="btn btn-primary text-xs mt-2 gap-1.5">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Novo Item
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($itens->count() > 0)
        <div class="px-5 py-3 border-t border-slate-100">
            <p class="text-xs text-slate-400">{{ $itens->count() }} item(ns) {{ $filtroTipo ? 'filtrado(s)' : '' }}</p>
        </div>
        @endif
    </div>

    {{-- ══ Modal Cadastro / Edição ══ --}}
    @if($modalAberto)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="fecharModal"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <x-lucide-tag class="w-4 h-4 text-blue-500" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $editandoId ? 'Editar Item' : 'Novo Item' }}
                    </h3>
                </div>
                <button wire:click="fecharModal" type="button"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Tipo --}}
                <div>
                    <label class="form-label">Tipo <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['consulta' => ['Consulta', 'stethoscope', 'blue'], 'exame' => ['Exame', 'flask-conical', 'violet'], 'atendimento' => ['Atendimento', 'heart-pulse', 'emerald']] as $t => [$label, $icon, $color])
                        <button type="button" wire:click="$set('tipo', '{{ $t }}')"
                            class="flex flex-col items-center gap-1.5 py-3 rounded-xl border-2 text-sm font-semibold transition-all
                                {{ $tipo === $t
                                    ? ($color === 'blue' ? 'border-blue-400 bg-blue-50 text-blue-600' : ($color === 'violet' ? 'border-violet-400 bg-violet-50 text-violet-600' : 'border-emerald-400 bg-emerald-50 text-emerald-600'))
                                    : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                            @if($t === 'consulta')
                                <x-lucide-stethoscope class="w-4 h-4" />
                            @elseif($t === 'exame')
                                <x-lucide-flask-conical class="w-4 h-4" />
                            @else
                                <x-lucide-heart-pulse class="w-4 h-4" />
                            @endif
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    @error('tipo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nome --}}
                <div>
                    <label class="form-label">Nome <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nome"
                        placeholder="Ex: Consulta Inicial, Hemograma Completo..."
                        class="form-input @error('nome') is-invalid @enderror" />
                    @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Valor + Duração --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Valor (R$) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">R$</span>
                            <input type="text" wire:model="valor"
                                placeholder="0,00"
                                style="padding-left: 2.5rem"
                                class="form-input @error('valor') is-invalid @enderror" />
                        </div>
                        @error('valor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Duração (min)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-lucide-clock class="w-3.5 h-3.5" />
                            </span>
                            <input type="number" wire:model="duracao_minutos" min="1" max="1440"
                                placeholder="Ex: 30"
                                style="padding-left: 2.25rem"
                                class="form-input @error('duracao_minutos') is-invalid @enderror" />
                        </div>
                        @error('duracao_minutos') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Código --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="form-label mb-0">Código</label>
                        @if(!$editandoId)
                        <span class="text-[10px] font-semibold text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                            <x-lucide-sparkles class="w-2.5 h-2.5" /> Gerado automaticamente
                        </span>
                        @endif
                    </div>
                    <input type="text" wire:model="codigo"
                        placeholder="CON-0001"
                        class="form-input @error('codigo') is-invalid @enderror" />
                    @error('codigo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Especialidade --}}
                <div>
                    <label class="form-label">Especialidade</label>
                    <x-select
                        wire="especialidade_id"
                        :options="$especialidades->map(fn($e) => ['value' => $e->id, 'label' => $e->nome])->toArray()"
                        placeholder="Sem especialidade"
                        icon="lucide-stethoscope"
                        :searchable="$especialidades->count() > 5"
                    />
                </div>

                {{-- Unidades (multi-select dropdown) --}}
                <div
                    x-data="{
                        open: false,
                        search: '',
                        all: {{ Js::from($unidades->map(fn($u) => ['id' => $u->id, 'nome' => $u->nome])->values()->toArray()) }},
                        dropStyle: {},

                        /* Lê direto do Livewire — sempre reativo após qualquer re-render */
                        get selected()   { return ($wire.unidades_selecionadas ?? []).map(Number); },
                        get isAll()      { return this.selected.length >= this.all.length && this.all.length > 0; },
                        get isNone()     { return this.selected.length === 0; },
                        get count()      { return this.selected.length; },
                        get filtered()   {
                            if (!this.search) return this.all;
                            const q = this.search.toLowerCase();
                            return this.all.filter(u => u.nome.toLowerCase().includes(q));
                        },
                        get summary() {
                            if (this.isNone)  return 'Nenhuma selecionada';
                            if (this.isAll)   return 'Todas as unidades';
                            if (this.count === 1) {
                                const u = this.all.find(u => u.id === this.selected[0]);
                                return u ? u.nome : '1 unidade';
                            }
                            return this.count + ' unidades selecionadas';
                        },

                        isSelected(id) { return this.selected.includes(Number(id)); },

                        async toggle(id)        { await $wire.toggleUnidade(id); },
                        async selectAll()       { await $wire.selecionarTodasUnidades(); },
                        async clearSelection()  { await $wire.limparSelecaoUnidades(); },

                        openDropdown() {
                            const rect = this.$refs.trigger.getBoundingClientRect();
                            const spaceBelow = window.innerHeight - rect.bottom;
                            const dropH = Math.min(320, this.all.length * 40 + 80);
                            const above = spaceBelow < dropH && rect.top > dropH;
                            this.dropStyle = {
                                position: 'fixed',
                                left: rect.left + 'px',
                                width: rect.width + 'px',
                                zIndex: 9999,
                                ...(above
                                    ? { bottom: (window.innerHeight - rect.top + 4) + 'px', top: 'auto' }
                                    : { top: (rect.bottom + 4) + 'px', bottom: 'auto' })
                            };
                            this.open = true;
                        }
                    }"
                    @keydown.escape.window="open = false; search = ''"
                    @scroll.window="open = false"
                >
                    <label class="form-label">Unidades disponíveis</label>

                    {{-- Trigger --}}
                    <button
                        x-ref="trigger"
                        type="button"
                        @click="open ? (open = false) : openDropdown()"
                        class="w-full flex items-center gap-2.5 pl-3 pr-2.5 h-[38px] bg-[#f8fafc] border-[1.5px] border-[#e2e8f0] rounded-[8px] text-left text-[13px] transition-all focus:outline-none select-none hover:border-slate-300 hover:bg-white"
                        :class="open ? 'border-blue-500 bg-white shadow-[0_0_0_3px_rgba(37,99,235,.09)]' : ''"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>

                        <span class="flex-1 truncate"
                            :class="isAll ? 'text-slate-400' : 'font-medium text-slate-700'"
                            x-text="summary">
                        </span>

                        <template x-if="!isNone">
                            <button type="button" @click.stop="clearSelection()"
                                class="w-5 h-5 rounded flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-colors flex-shrink-0">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>
                        </template>

                        <svg class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-150"
                            :class="open ? 'rotate-180 text-blue-500' : 'text-slate-400'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown (teleportado para escapar do overflow do modal) --}}
                    <template x-teleport="body">
                        <div
                            x-show="open"
                            :style="dropStyle"
                            @click.outside="open = false; search = ''"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-y-95"
                            x-transition:enter-end="opacity-100 scale-y-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-y-100"
                            x-transition:leave-end="opacity-0 scale-y-95"
                            style="display:none"
                            class="bg-white border border-slate-200 rounded-xl shadow-[0_8px_30px_rgba(0,0,0,.12)] overflow-hidden origin-top"
                        >
                            {{-- Search + ações --}}
                            <div class="px-3 pt-2.5 pb-2 border-b border-slate-100 space-y-2">
                                <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-2.5 h-8 border border-slate-200 focus-within:border-blue-400 focus-within:bg-white transition-colors">
                                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                    </svg>
                                    <input x-model="search" type="text" placeholder="Buscar unidade..."
                                        x-effect="if(open) $nextTick(() => $el.focus())"
                                        @click.stop
                                        class="flex-1 bg-transparent border-none outline-none text-xs text-slate-700 placeholder-slate-400" />
                                    <button x-show="search" @click.stop="search = ''" type="button" class="text-slate-400 hover:text-slate-600">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                {{-- Selecionar todas / Limpar seleção --}}
                                <div class="flex items-center justify-between px-0.5">
                                    <button type="button" @click.stop="selectAll()"
                                        class="text-[11px] font-semibold transition-colors flex items-center gap-1"
                                        :class="isAll ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600'">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                                        </svg>
                                        Todas as unidades
                                    </button>
                                    <button x-show="!isNone" type="button" @click.stop="clearSelection()"
                                        class="text-[11px] font-semibold text-slate-400 hover:text-red-500 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/></svg>
                                        Limpar
                                    </button>
                                </div>
                            </div>

                            {{-- Lista --}}
                            <div class="max-h-56 overflow-y-auto py-1">
                                <template x-if="filtered.length === 0">
                                    <p class="px-4 py-3 text-xs text-slate-400 text-center">Nenhuma unidade encontrada</p>
                                </template>

                                <template x-for="uni in filtered" :key="uni.id">
                                    <button type="button" @click.stop="toggle(uni.id)"
                                        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-[13px] text-left transition-colors"
                                        :class="isSelected(uni.id) ? 'bg-blue-50' : 'hover:bg-slate-50'">

                                        {{-- Checkbox visual --}}
                                        <div class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                            :class="isSelected(uni.id)
                                                ? 'bg-blue-500 border-blue-500'
                                                : 'border-slate-300 bg-white'">
                                            <svg x-show="isSelected(uni.id)" class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>

                                        <span class="flex-1 truncate"
                                            :class="isSelected(uni.id) ? 'font-semibold text-blue-700' : 'font-medium text-slate-600'"
                                            x-text="uni.nome">
                                        </span>
                                    </button>
                                </template>
                            </div>

                            {{-- Footer --}}
                            <div class="px-3 py-2.5 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400" x-text="all.length + ' unidades disponíveis'"></span>
                                <button type="button" @click="open = false"
                                    class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                    Concluir
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Chips das selecionadas --}}
                    <template x-if="!isAll">
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <template x-for="id in $wire.unidades_selecionadas" :key="id">
                                <span class="inline-flex items-center gap-1 pl-2 pr-1 py-0.5 bg-blue-50 border border-blue-200 text-blue-700 text-[11px] font-medium rounded-lg">
                                    <span x-text="all.find(u => u.id === id)?.nome ?? id"></span>
                                    <button type="button" @click.stop="toggle(id)"
                                        class="w-4 h-4 rounded flex items-center justify-center hover:bg-blue-200 transition-colors">
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Descrição --}}
                <div>
                    <label class="form-label">Descrição / Observações</label>
                    <textarea wire:model="descricao" rows="3"
                        placeholder="Informações adicionais sobre este item..."
                        class="form-input resize-none"></textarea>
                </div>

                {{-- Ativo --}}
                <x-check-item wire="ativo" icon="circle-check" label="Item ativo na tabela de preços" />
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl sticky bottom-0">
                <button type="button" wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                <button type="button" wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                    <svg wire:loading wire:target="salvar" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <x-lucide-check wire:loading.remove wire:target="salvar" class="w-3.5 h-3.5" />
                    <span wire:loading wire:target="salvar">Salvando...</span>
                    <span wire:loading.remove wire:target="salvar">{{ $editandoId ? 'Salvar Alterações' : 'Criar Item' }}</span>
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
