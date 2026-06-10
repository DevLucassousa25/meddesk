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
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Salas</h1>
            <p class="text-sm text-slate-400 mt-0.5">Gerencie as salas e consultórios da clínica</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Toggle visualização --}}
            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-10 bg-white">
                <button wire:click="$set('visualizacao', 'tabela')" type="button" title="Tabela"
                    class="w-10 h-10 flex items-center justify-center transition-colors {{ $visualizacao === 'tabela' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}">
                    <x-lucide-list class="w-4 h-4" />
                </button>
                <button wire:click="$set('visualizacao', 'cards')" type="button" title="Cards"
                    class="w-10 h-10 flex items-center justify-center transition-colors {{ $visualizacao === 'cards' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}">
                    <x-lucide-layout-grid class="w-4 h-4" />
                </button>
            </div>

            {{-- Exportar CSV --}}
            <button wire:click="exportarCsv" wire:loading.attr="disabled" type="button"
                class="h-10 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <svg wire:loading wire:target="exportarCsv" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <x-lucide-download wire:loading.remove wire:target="exportarCsv" class="w-4 h-4" />
                <span wire:loading wire:target="exportarCsv">Exportando...</span>
                <span wire:loading.remove wire:target="exportarCsv">Exportar CSV</span>
            </button>

            {{-- Nova Sala --}}
            <button type="button" wire:click="abrirCadastro"
                class="btn btn-primary gap-2 h-10 px-5 text-sm rounded-xl shadow-sm shadow-blue-200">
                <x-lucide-plus class="w-4 h-4" />
                Nova Sala
            </button>
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <x-lucide-door-open class="w-4 h-4 text-blue-500" />
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Total</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5">salas cadastradas</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-500" />
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Ativas</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ $stats['ativas'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5">em operação</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center">
                    <x-lucide-x-circle class="w-4 h-4 text-slate-400" />
                </div>
                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Inativas</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ $stats['inativas'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5">desativadas</div>
        </div>
    </div>

    {{-- Barra de filtros --}}
    <div x-data="{ expanded: false }" class="bg-white border border-slate-100 rounded-xl mb-4">

        {{-- Linha principal --}}
        <div class="flex items-center divide-x divide-slate-100 flex-wrap">

            {{-- Busca --}}
            <div class="flex items-center gap-2.5 px-4 flex-1 h-11 min-w-[180px]">
                <x-lucide-search class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                <input
                    wire:model.live.debounce.300ms="busca"
                    type="text"
                    placeholder="Buscar por nome..."
                    class="bg-transparent border-none outline-none text-sm text-slate-700 placeholder-slate-400 w-full"
                />
                @if($busca)
                    <button wire:click="$set('busca', '')" class="text-slate-300 hover:text-slate-500 flex-shrink-0">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                @endif
            </div>

            {{-- Pills status --}}
            <div class="flex items-center h-11 px-3 gap-1 flex-shrink-0">
                @foreach(['' => 'Todas', 'ativo' => 'Ativas', 'inativo' => 'Inativas'] as $val => $lbl)
                    <button wire:click="$set('filtroStatus', '{{ $val }}')" type="button"
                        class="px-3 h-7 rounded-lg text-xs font-medium transition-colors whitespace-nowrap
                            {{ $filtroStatus === $val ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100' }}">
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>

            {{-- Botão filtros avançados --}}
            <div class="flex items-center h-11 px-3 gap-2 flex-shrink-0">
                <button type="button" @click="expanded = !expanded"
                    class="flex items-center gap-1.5 px-3 h-7 rounded-lg text-xs font-medium transition-colors"
                    :class="expanded ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-100'">
                    <x-lucide-sliders-horizontal class="w-3.5 h-3.5" />
                    Filtros
                    @if($filtroTipo || $filtroUnidade || $filtroCapacidade || $filtroStatus)
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 ml-0.5"></span>
                    @endif
                </button>

                @if($busca || $filtroStatus || $filtroTipo || $filtroUnidade || $filtroCapacidade)
                    <button wire:click="limparFiltros" type="button"
                        class="flex items-center gap-1 text-xs text-slate-400 hover:text-red-400 transition-colors">
                        <x-lucide-x class="w-3 h-3" /> Limpar
                    </button>
                @endif
            </div>

            {{-- Por página --}}
            <div class="flex items-center h-11 px-4 flex-shrink-0">
                <select wire:model.live="porPagina" class="text-xs text-slate-500 bg-transparent border-none outline-none cursor-pointer">
                    <option value="10">10 / pág.</option>
                    <option value="15">15 / pág.</option>
                    <option value="30">30 / pág.</option>
                    <option value="50">50 / pág.</option>
                </select>
            </div>
        </div>

        {{-- Filtros avançados (colapsável) --}}
        <div
            x-show="expanded"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="border-t border-slate-100 px-4 py-3 grid grid-cols-2 sm:grid-cols-4 gap-3 relative z-30"
            style="display:none"
        >
            @php
                $optsTipoFiltro = array_merge(
                    [['value'=>'','label'=>'Todos os tipos']],
                    collect(\App\Livewire\Configuracoes\Salas::$tipos)->map(fn($l,$k) => ['value'=>$k,'label'=>$l])->values()->toArray()
                );
                $optsUnidadeFiltro = array_merge(
                    [['value'=>'','label'=>'Todas as unidades']],
                    collect($unidades)->map(fn($u) => ['value'=>(string)$u['id'],'label'=>$u['nome']])->toArray()
                );
                $optsCapacidade = [
                    ['value'=>'',    'label'=>'Qualquer capacidade'],
                    ['value'=>'1',   'label'=>'Individual (1 pessoa)'],
                    ['value'=>'2-4', 'label'=>'Pequena (2–4 pessoas)'],
                    ['value'=>'5-9', 'label'=>'Média (5–9 pessoas)'],
                    ['value'=>'10+', 'label'=>'Grande (10+ pessoas)'],
                ];
                $optsAtivo = [
                    ['value'=>'',       'label'=>'Ativas e inativas'],
                    ['value'=>'ativo',  'label'=>'Somente ativas'],
                    ['value'=>'inativo','label'=>'Somente inativas'],
                ];
            @endphp

            {{-- Tipo --}}
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Sala</label>
                <x-select wire="filtroTipo" :options="$optsTipoFiltro" placeholder="Todos os tipos" icon="lucide-layout-grid" />
            </div>

            {{-- Unidade --}}
            @if(count($unidades))
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Unidade</label>
                <x-select wire="filtroUnidade" :options="$optsUnidadeFiltro" placeholder="Todas as unidades" icon="lucide-building-2" />
            </div>
            @endif

            {{-- Capacidade --}}
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Capacidade</label>
                <x-select wire="filtroCapacidade" :options="$optsCapacidade" placeholder="Qualquer" icon="lucide-users" />
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                <x-select wire="filtroStatus" :options="$optsAtivo" placeholder="Ativas e inativas" icon="lucide-circle-check" />
            </div>
        </div>
    </div>

    {{-- Chips de filtros ativos --}}
    @php
        $chips = [];
        if($busca)            $chips[] = ['label' => '"'.$busca.'"',                                                                         'clear' => 'busca',             'val' => ''];
        if($filtroStatus)     $chips[] = ['label' => ['ativo'=>'Ativas','inativo'=>'Inativas'][$filtroStatus],                               'clear' => 'filtroStatus',      'val' => ''];
        if($filtroTipo)       $chips[] = ['label' => 'Tipo: '.(\App\Livewire\Configuracoes\Salas::$tipos[$filtroTipo] ?? $filtroTipo),        'clear' => 'filtroTipo',        'val' => ''];
        if($filtroUnidade)    $chips[] = ['label' => 'Unidade: '.(collect($unidades)->firstWhere('id',(int)$filtroUnidade)['nome']??'—'),     'clear' => 'filtroUnidade',     'val' => ''];
        if($filtroCapacidade) $chips[] = ['label' => 'Cap.: '.['1'=>'Individual','2-4'=>'2–4 pessoas','5-9'=>'5–9 pessoas','10+'=>'10+ pessoas'][$filtroCapacidade], 'clear' => 'filtroCapacidade', 'val' => ''];
    @endphp
    @if(count($chips))
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Filtros ativos:</span>
        @foreach($chips as $chip)
            <button
                wire:click="$set('{{ $chip['clear'] }}', '{{ $chip['val'] }}')"
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-colors group">
                {{ $chip['label'] }}
                <x-lucide-x class="w-3 h-3 opacity-40 group-hover:opacity-100" />
            </button>
        @endforeach
        @if(count($chips) > 1)
            <button wire:click="limparFiltros" type="button" class="text-xs text-slate-400 hover:text-red-400 transition-colors font-medium">
                Limpar todos
            </button>
        @endif
    </div>
    @endif

    {{-- ══ Bulk action bar ══ --}}
    @if(count($selecionados) > 0)
    <div class="flex flex-col gap-0 mb-3 rounded-xl overflow-hidden shadow-sm shadow-blue-200">
        <div class="flex items-center gap-3 px-4 py-2.5 bg-blue-600 text-white text-sm">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded bg-white/20 flex items-center justify-center">
                    <x-lucide-check class="w-3 h-3" />
                </div>
                <span class="font-semibold">
                    @if($todosRegistros)
                        Todas as {{ number_format($totalFiltrado) }} salas selecionadas
                    @else
                        {{ count($selecionados) }} selecionada(s)
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <button wire:click="exportarSelecionadas" wire:loading.attr="disabled" type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-download class="w-3.5 h-3.5" /> Exportar
                </button>
                @if($this->selecionadasTodasInativas)
                <button x-data
                    @click="$store.modal.confirm({
                        type: 'info',
                        title: 'Ativar salas',
                        message: 'Deseja ativar {{ $todosRegistros ? $totalFiltrado : count($selecionados) }} sala(s)?',
                        confirmText: 'Ativar',
                        onConfirm: () => $wire.ativarSelecionadas()
                    })"
                    type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-check-circle class="w-3.5 h-3.5" /> Ativar
                </button>
                @else
                <button x-data
                    @click="$store.modal.confirm({
                        type: 'warning',
                        title: 'Desativar salas',
                        message: 'Deseja desativar {{ $todosRegistros ? $totalFiltrado : count($selecionados) }} sala(s)?',
                        confirmText: 'Desativar',
                        onConfirm: () => $wire.desativarSelecionadas()
                    })"
                    type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-x-circle class="w-3.5 h-3.5" /> Desativar
                </button>
                @endif
                <button wire:click="limparSelecao" type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-x class="w-3.5 h-3.5" /> Cancelar
                </button>
            </div>
        </div>
        @if(!$todosRegistros && count($selecionados) >= count($salas->items()) && $totalFiltrado > count($selecionados))
        <div class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 border-x border-b border-blue-200 text-xs text-blue-700">
            <span>As {{ count($selecionados) }} salas desta página estão selecionadas.</span>
            <button wire:click="selecionarTodos" type="button" class="font-semibold underline hover:text-blue-900 transition-colors">
                Selecionar todas as {{ number_format($totalFiltrado) }} salas
            </button>
        </div>
        @endif
    </div>
    @endif

    {{-- ══ Tabela ══ --}}
    @if($visualizacao === 'tabela')
    @php
        $idsVisiveis   = $salas->pluck('id')->toArray();
        $todosVisiveis = count($idsVisiveis) > 0 && count(array_intersect($idsVisiveis, $selecionados)) === count($idsVisiveis);
    @endphp
    <div class="bg-white border border-slate-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="pl-4 pr-2 py-3 w-8">
                            <input type="checkbox"
                                x-data
                                wire:click="selecionarTodosVisiveis({{ json_encode($idsVisiveis) }})"
                                :checked="{{ json_encode($todosVisiveis) }}"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer" />
                        </th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sala</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Unidade</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Capacidade</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-3 pr-4 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($salas as $sala)
                    @php $isSelecionada = in_array($sala->id, $selecionados); @endphp
                    <tr class="hover:bg-slate-50 transition-colors group {{ $isSelecionada ? 'bg-blue-50/50' : '' }}">

                        {{-- Checkbox --}}
                        <td class="pl-4 pr-2 py-3">
                            <input type="checkbox"
                                x-data
                                wire:click="toggleSelecionado({{ $sala->id }})"
                                :checked="$wire.selecionados.includes({{ $sala->id }})"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer" />
                        </td>

                        {{-- Nome --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                     style="background:{{ $sala->cor }}22; color:{{ $sala->cor }}">
                                    <x-lucide-door-open class="w-3.5 h-3.5" />
                                </div>
                                <div>
                                    <span class="font-medium text-slate-700">{{ $sala->nome }}</span>
                                    @if($sala->descricao)
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[220px]">{{ $sala->descricao }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Tipo --}}
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($sala->tipo)
                                <span class="text-xs text-slate-500">
                                    {{ \App\Livewire\Configuracoes\Salas::$tipos[$sala->tipo] ?? $sala->tipo }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- Unidade --}}
                        <td class="px-4 py-3 text-slate-500 hidden md:table-cell text-xs">
                            {{ $sala->unidade?->nome ?? '—' }}
                        </td>

                        {{-- Capacidade --}}
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                <x-lucide-users class="w-3.5 h-3.5 text-slate-400" />
                                {{ $sala->capacidade }} {{ $sala->capacidade === 1 ? 'pessoa' : 'pessoas' }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <button type="button" x-data
                                @click="$store.modal.confirm({
                                    type: '{{ $sala->ativo ? 'warning' : 'info' }}',
                                    title: '{{ $sala->ativo ? 'Desativar sala' : 'Ativar sala' }}',
                                    message: 'Deseja {{ $sala->ativo ? 'desativar' : 'ativar' }} a sala \'{{ $sala->nome }}\'?',
                                    confirmText: '{{ $sala->ativo ? 'Desativar' : 'Ativar' }}',
                                    onConfirm: () => $wire.toggleAtivo({{ $sala->id }})
                                })">
                                @if($sala->ativo)
                                    <span class="badge badge-green">Ativa</span>
                                @else
                                    <span class="badge badge-gray">Inativa</span>
                                @endif
                            </button>
                        </td>

                        {{-- Ações --}}
                        <td class="pr-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" wire:click="editar({{ $sala->id }})" title="Editar"
                                    class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors">
                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                </button>
                                <button type="button" x-data
                                    @click="$store.modal.confirm({
                                        type: 'delete',
                                        title: 'Excluir sala',
                                        message: 'Deseja excluir a sala \'{{ $sala->nome }}\'? Esta ação não poderá ser desfeita.',
                                        confirmText: 'Excluir',
                                        onConfirm: () => $wire.excluir({{ $sala->id }})
                                    })"
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
                                    <x-lucide-door-open class="w-5 h-5 text-slate-300" />
                                </div>
                                <p class="text-sm font-medium text-slate-500">Nenhuma sala encontrada</p>
                                <p class="text-xs text-slate-400">
                                    @if($busca || $filtroUnidade || $filtroStatus)
                                        Tente ajustar os filtros.
                                    @else
                                        Cadastre a primeira sala da clínica.
                                    @endif
                                </p>
                                @if(!$busca && !$filtroUnidade && !$filtroStatus)
                                    <button type="button" wire:click="abrirCadastro" class="btn btn-primary text-xs mt-2 gap-1.5">
                                        <x-lucide-plus class="w-3.5 h-3.5" /> Nova Sala
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salas->total() > 0)
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-xs text-slate-400">
                {{ $salas->firstItem() }}–{{ $salas->lastItem() }} de {{ $salas->total() }} sala(s)
                @if(count($selecionados) > 0)
                    <span class="ml-2 text-blue-500 font-medium">· {{ count($selecionados) }} selecionada(s)</span>
                @endif
            </p>
            <x-pagination :paginator="$salas" />
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         VISUALIZAÇÃO CARDS
         ══════════════════════════════════════════════════════ --}}
    @if($visualizacao === 'cards')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($salas as $sala)
        @php $isSelecionada = in_array($sala->id, $selecionados); @endphp

        <div class="bg-white border rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col {{ $isSelecionada ? 'border-blue-300 ring-2 ring-blue-100' : 'border-slate-100' }}">

            {{-- Hero --}}
            <div class="relative flex-shrink-0 h-[110px]"
                 style="background: linear-gradient(145deg, {{ $sala->cor }}22 0%, {{ $sala->cor }}11 100%)">

                {{-- Checkbox --}}
                <label class="absolute top-3 left-3 z-10 cursor-pointer">
                    <input type="checkbox"
                        x-data
                        wire:click="toggleSelecionado({{ $sala->id }})"
                        :checked="$wire.selecionados.includes({{ $sala->id }})"
                        class="w-4 h-4 rounded border-white/80 text-blue-600 cursor-pointer shadow-sm"
                        style="background: rgba(255,255,255,0.85)" />
                </label>

                {{-- Status --}}
                <div class="absolute top-3 right-3 z-10">
                    <button type="button" wire:click="toggleAtivo({{ $sala->id }})">
                        @if($sala->ativo)
                            <span class="badge badge-green">Ativa</span>
                        @else
                            <span class="badge badge-gray">Inativa</span>
                        @endif
                    </button>
                </div>

                {{-- Círculos decorativos --}}
                <div class="absolute inset-0 overflow-hidden rounded-t-2xl pointer-events-none">
                    <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full opacity-20" style="background:{{ $sala->cor }}"></div>
                    <div class="absolute -top-6 -left-6 w-20 h-20 rounded-full opacity-10" style="background:{{ $sala->cor }}"></div>
                </div>

                {{-- Ícone central --}}
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 z-10">
                    <div class="w-16 h-16 rounded-2xl border-[3px] border-white shadow-lg flex items-center justify-center"
                         style="background:{{ $sala->cor }}22; color:{{ $sala->cor }}">
                        <x-lucide-door-open class="w-7 h-7" />
                    </div>
                </div>
            </div>

            {{-- Corpo --}}
            <div class="px-4 pt-10 pb-3 flex-1 flex flex-col">

                {{-- Nome --}}
                <div class="mb-3 text-center">
                    <span class="font-bold text-slate-800 leading-snug block truncate text-sm">{{ $sala->nome }}</span>
                    @if($sala->descricao)
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $sala->descricao }}</p>
                    @endif
                </div>

                {{-- Chips --}}
                <div class="flex flex-wrap justify-center gap-1.5 mb-3">
                    @if($sala->tipo)
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            <x-lucide-tag class="w-2.5 h-2.5" />
                            {{ \App\Livewire\Configuracoes\Salas::$tipos[$sala->tipo] ?? $sala->tipo }}
                        </span>
                    @endif
                    @if($sala->unidade)
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">
                            <x-lucide-building-2 class="w-2.5 h-2.5" />
                            {{ $sala->unidade->nome }}
                        </span>
                    @endif
                </div>

                {{-- Grid capacidade / cor --}}
                <div class="mt-auto rounded-xl bg-slate-50 grid grid-cols-2 divide-x divide-slate-100 overflow-hidden">
                    <div class="px-3 py-2.5">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Capacidade</div>
                        <div class="flex items-center gap-1 text-xs text-slate-700">
                            <x-lucide-users class="w-3 h-3 text-slate-400" />
                            {{ $sala->capacidade }} {{ $sala->capacidade === 1 ? 'pessoa' : 'pessoas' }}
                        </div>
                    </div>
                    <div class="px-3 py-2.5">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Cor</div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-3.5 h-3.5 rounded-full border border-white shadow-sm" style="background:{{ $sala->cor }}"></div>
                            <span class="text-xs text-slate-500 font-mono">{{ $sala->cor }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rodapé ações --}}
            <div class="border-t border-slate-100 px-3 py-2.5 flex items-center justify-end gap-1">
                <button type="button" wire:click="editar({{ $sala->id }})" title="Editar"
                    class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors">
                    <x-lucide-pencil class="w-3.5 h-3.5" />
                </button>
                <button type="button" x-data
                    @click="$store.modal.confirm({
                        type: 'delete',
                        title: 'Excluir sala',
                        message: 'Deseja excluir a sala \'{{ $sala->nome }}\'? Esta ação não poderá ser desfeita.',
                        confirmText: 'Excluir',
                        onConfirm: () => $wire.excluir({{ $sala->id }})
                    })"
                    title="Excluir"
                    class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors">
                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>

        @empty
            <div class="col-span-full py-14 text-center">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-1">
                        <x-lucide-door-open class="w-5 h-5 text-slate-300" />
                    </div>
                    <p class="text-sm font-medium text-slate-500">Nenhuma sala encontrada</p>
                    <p class="text-xs text-slate-400">
                        @if($busca || $filtroUnidade || $filtroStatus) Tente ajustar os filtros. @else Cadastre a primeira sala da clínica. @endif
                    </p>
                    @if(!$busca && !$filtroUnidade && !$filtroStatus)
                        <button type="button" wire:click="abrirCadastro" class="btn btn-primary text-xs mt-2 gap-1.5">
                            <x-lucide-plus class="w-3.5 h-3.5" /> Nova Sala
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    @if($salas->total() > 0)
    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400">
            {{ $salas->firstItem() }}–{{ $salas->lastItem() }} de {{ $salas->total() }} sala(s)
        </p>
        <x-pagination :paginator="$salas" />
    </div>
    @endif
    @endif

</div>
