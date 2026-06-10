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

    @php
        $avatarColors = [
            'A'=>['bg'=>'#dbeafe','text'=>'#1d4ed8'], 'B'=>['bg'=>'#fce7f3','text'=>'#9d174d'],
            'C'=>['bg'=>'#d1fae5','text'=>'#065f46'], 'D'=>['bg'=>'#fef9c3','text'=>'#854d0e'],
            'E'=>['bg'=>'#ede9fe','text'=>'#5b21b6'], 'F'=>['bg'=>'#fee2e2','text'=>'#991b1b'],
            'G'=>['bg'=>'#e0f2fe','text'=>'#0369a1'], 'H'=>['bg'=>'#fef3c7','text'=>'#92400e'],
            'I'=>['bg'=>'#dcfce7','text'=>'#166534'], 'J'=>['bg'=>'#ede9fe','text'=>'#6d28d9'],
            'K'=>['bg'=>'#fce7f3','text'=>'#831843'], 'L'=>['bg'=>'#e0f2fe','text'=>'#075985'],
            'M'=>['bg'=>'#fef9c3','text'=>'#713f12'], 'N'=>['bg'=>'#dbeafe','text'=>'#1e40af'],
            'O'=>['bg'=>'#d1fae5','text'=>'#064e3b'], 'P'=>['bg'=>'#fee2e2','text'=>'#7f1d1d'],
            'Q'=>['bg'=>'#ede9fe','text'=>'#4c1d95'], 'R'=>['bg'=>'#e0f2fe','text'=>'#0c4a6e'],
            'S'=>['bg'=>'#fce7f3','text'=>'#9d174d'], 'T'=>['bg'=>'#fef9c3','text'=>'#78350f'],
            'U'=>['bg'=>'#d1fae5','text'=>'#065f46'], 'V'=>['bg'=>'#dbeafe','text'=>'#1e3a8a'],
            'W'=>['bg'=>'#fef3c7','text'=>'#92400e'], 'X'=>['bg'=>'#ede9fe','text'=>'#5b21b6'],
            'Y'=>['bg'=>'#dcfce7','text'=>'#14532d'], 'Z'=>['bg'=>'#fce7f3','text'=>'#831843'],
        ];
    @endphp

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Equipe</h1>
            <p class="text-sm text-slate-400 mt-0.5">Gerencie os profissionais e funcionários da clínica</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Toggle visualização --}}
            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-10 bg-white">
                <button
                    wire:click="$set('visualizacao', 'tabela')"
                    type="button"
                    title="Tabela"
                    class="w-10 h-10 flex items-center justify-center transition-colors {{ $visualizacao === 'tabela' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}"
                ><x-lucide-list class="w-4 h-4" /></button>
                <button
                    wire:click="$set('visualizacao', 'cards')"
                    type="button"
                    title="Cards"
                    class="w-10 h-10 flex items-center justify-center transition-colors {{ $visualizacao === 'cards' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}"
                ><x-lucide-layout-grid class="w-4 h-4" /></button>
            </div>

            {{-- Exportar CSV --}}
            <button
                wire:click="exportarCsv"
                wire:loading.attr="disabled"
                type="button"
                class="h-10 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2"
            >
                <svg wire:loading wire:target="exportarCsv" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <x-lucide-download wire:loading.remove wire:target="exportarCsv" class="w-4 h-4" />
                <span wire:loading wire:target="exportarCsv">Exportando...</span>
                <span wire:loading.remove wire:target="exportarCsv">Exportar CSV</span>
            </button>

            {{-- Novo Profissional --}}
            <a href="{{ route('profissionais.cadastro') }}" class="btn btn-primary gap-2 h-10 px-5 text-sm rounded-xl shadow-sm shadow-blue-200">
                <x-lucide-user-plus class="w-4 h-4" />
                Novo Profissional
            </a>
        </div>
    </div>

    {{-- ── Cards de resumo ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <x-lucide-users class="w-4 h-4 text-blue-500" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Total</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">profissionais cadastrados</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <x-lucide-user-check class="w-4 h-4 text-emerald-500" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Ativos</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['ativos']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">em atividade</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center">
                    <x-lucide-user-x class="w-4 h-4 text-slate-400" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Inativos</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['inativos']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">sem atividade</div>
        </div>
    </div>

    {{-- ── Barra de filtros ── --}}
    <div x-data="{ expanded: false }" class="bg-white border border-slate-100 rounded-xl mb-4">

        {{-- Linha principal --}}
        <div class="flex items-center divide-x divide-slate-100 flex-wrap">
            {{-- Busca --}}
            <div class="flex items-center gap-2.5 px-4 flex-1 h-11 min-w-[200px]">
                <x-lucide-search class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                <input
                    wire:model.live.debounce.300ms="busca"
                    type="text"
                    placeholder="Buscar por nome, CPF, e-mail ou código..."
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
                @foreach(['' => 'Todos', 'ativo' => 'Ativos', 'inativo' => 'Inativos'] as $val => $lbl)
                    <button
                        wire:click="$set('filtroStatus', '{{ $val }}')"
                        type="button"
                        class="px-3 h-7 rounded-lg text-xs font-medium transition-colors whitespace-nowrap {{ $filtroStatus === $val ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100' }}"
                    >{{ $lbl }}</button>
                @endforeach
            </div>

            {{-- Filtros avançados --}}
            <div class="flex items-center h-11 px-3 gap-2 flex-shrink-0">
                <button
                    type="button"
                    @click="expanded = !expanded"
                    class="flex items-center gap-1.5 px-3 h-7 rounded-lg text-xs font-medium transition-colors"
                    :class="expanded ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-100'"
                >
                    <x-lucide-sliders-horizontal class="w-3.5 h-3.5" />
                    Filtros
                    @if($filtroEspecialidade || $filtroUnidade || $filtroComConselho)
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 ml-0.5"></span>
                    @endif
                </button>

                @if($this->temFiltrosAtivos())
                    <button
                        wire:click="limparFiltros"
                        type="button"
                        class="flex items-center gap-1 text-xs text-slate-400 hover:text-red-400 transition-colors"
                    >
                        <x-lucide-x class="w-3 h-3" /> Limpar
                    </button>
                @endif
            </div>

            {{-- Por página --}}
            <div class="flex items-center h-11 px-4 flex-shrink-0">
                <select wire:model.live="porPagina" class="text-xs text-slate-500 bg-transparent border-none outline-none cursor-pointer">
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
            class="border-t border-slate-100 px-4 py-3 grid grid-cols-2 sm:grid-cols-3 gap-3 relative z-30"
            style="display:none"
        >
            @php
                $optsEsp = array_merge(
                    [['value'=>'','label'=>'Todas as especialidades']],
                    collect($especialidades)->map(fn($e) => ['value'=>(string)$e['id'],'label'=>$e['nome']])->toArray()
                );
                $optsUnidade = array_merge(
                    [['value'=>'','label'=>'Todas as unidades']],
                    collect($unidades)->map(fn($u) => ['value'=>$u['nome'],'label'=>$u['nome']])->toArray()
                );
            @endphp

            {{-- Especialidade --}}
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Especialidade</label>
                <x-select
                    wire="filtroEspecialidade"
                    :options="$optsEsp"
                    placeholder="Todas as especialidades"
                    :searchable="count($especialidades) > 5"
                    icon="lucide-award"
                />
            </div>

            {{-- Unidade --}}
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Unidade</label>
                <x-select
                    wire="filtroUnidade"
                    :options="$optsUnidade"
                    placeholder="Todas as unidades"
                    icon="lucide-building-2"
                />
            </div>

            {{-- Com conselho --}}
            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors w-full">
                    <input wire:model.live="filtroComConselho" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 flex-shrink-0" />
                    <div>
                        <span class="text-xs font-semibold text-slate-700">Com conselho</span>
                        <p class="text-[10px] text-slate-400">CRM, CRO, etc.</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- ── Chips de filtros ativos ── --}}
    @php
        $chips = [];
        if($busca)               $chips[] = ['label'=>'"'.$busca.'"',                                                                         'clear'=>'busca',               'val'=>''];
        if($filtroStatus)        $chips[] = ['label'=>['ativo'=>'Ativos','inativo'=>'Inativos'][$filtroStatus],                                'clear'=>'filtroStatus',        'val'=>''];
        if($filtroEspecialidade) $chips[] = ['label'=>'Esp.: '.(collect($especialidades)->firstWhere('id',(int)$filtroEspecialidade)['nome']??'—'), 'clear'=>'filtroEspecialidade','val'=>''];
        if($filtroUnidade)       $chips[] = ['label'=>'Unidade: '.$filtroUnidade,                                                             'clear'=>'filtroUnidade',       'val'=>''];
        if($filtroComConselho)   $chips[] = ['label'=>'Com conselho profissional',                                                            'clear'=>'filtroComConselho',   'val'=>false];
    @endphp
    @if(count($chips))
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Filtros ativos:</span>
        @foreach($chips as $chip)
            <button
                wire:click="$set('{{ $chip['clear'] }}', {{ is_bool($chip['val']) ? ($chip['val'] ? 'true' : 'false') : "'".$chip['val']."'" }})"
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-colors group"
            >
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
                <span class="font-semibold">{{ count($selecionados) }} selecionado(s)</span>
            </div>
            <div class="flex items-center gap-2 ml-auto flex-wrap">
                <button wire:click="exportarSelecionados" wire:loading.attr="disabled" type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-download class="w-3.5 h-3.5" /> Exportar
                </button>
                @if($this->selecionadosTodosInativos)
                <button
                    x-data
                    @click="$store.modal.confirm({
                        type: 'info',
                        title: 'Ativar profissionais',
                        message: 'Deseja ativar {{ count($selecionados) }} profissional(is)?',
                        confirmText: 'Ativar',
                        onConfirm: () => $wire.ativarSelecionados()
                    })"
                    wire:loading.attr="disabled" type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-user-check class="w-3.5 h-3.5" /> Ativar
                </button>
                @else
                <button
                    x-data
                    @click="$store.modal.confirm({
                        type: 'warning',
                        title: 'Inativar profissionais',
                        message: 'Deseja inativar {{ count($selecionados) }} profissional(is)? Esta ação poderá ser revertida.',
                        confirmText: 'Inativar',
                        onConfirm: () => $wire.inativarSelecionados()
                    })"
                    wire:loading.attr="disabled" type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-user-x class="w-3.5 h-3.5" /> Inativar
                </button>
                @endif
                <button wire:click="limparSelecao" type="button"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                    <x-lucide-x class="w-3.5 h-3.5" /> Cancelar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         VISUALIZAÇÃO TABELA
         ══════════════════════════════════════════════════════ --}}
    @if($visualizacao === 'tabela')
    @php
        $idsVisiveis   = $profissionais->pluck('id')->toArray();
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
                        <th class="text-left px-3 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Profissional</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">CPF</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">E-mail</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Conselho</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden xl:table-cell">Celular</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-3 pr-4 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($profissionais as $p)
                    @php
                        $inicial = mb_strtoupper(mb_substr($p->nome, 0, 1));
                        $cor = $avatarColors[$inicial] ?? ['bg'=>'#dbeafe','text'=>'#1d4ed8'];
                        $whatsNum = preg_replace('/\D/', '', $p->celular1 ?: $p->telefone ?: '');
                        $whatsUrl = $whatsNum ? 'https://wa.me/55'.$whatsNum : null;
                        $isSelecionado = in_array($p->id, $selecionados);
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors group {{ $isSelecionado ? 'bg-blue-50/50' : '' }}">

                        {{-- Checkbox --}}
                        <td class="pl-4 pr-2 py-3">
                            <input type="checkbox"
                                x-data
                                wire:click="toggleSelecionado({{ $p->id }})"
                                :checked="$wire.selecionados.includes({{ $p->id }})"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer" />
                        </td>

                        {{-- Profissional --}}
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden text-xs font-bold"
                                         style="background:{{ $cor['bg'] }};color:{{ $cor['text'] }}">
                                        @if($p->foto)
                                            <img src="{{ Storage::url($p->foto) }}" class="w-full h-full object-cover" />
                                        @else
                                            {{ $inicial }}
                                        @endif
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('profissionais.detalhes', $p->id) }}"
                                       class="font-medium text-slate-800 hover:text-blue-600 transition-colors leading-tight truncate block max-w-[160px]">
                                        {{ $p->nome }}
                                    </a>
                                    @if($p->identificacao)
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->identificacao }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- CPF --}}
                        <td class="px-4 py-3 text-slate-500 hidden sm:table-cell text-xs">
                            {{ $p->cpf ? \Str::mask($p->cpf, '*', 3, 6) : '—' }}
                        </td>

                        {{-- E-mail --}}
                        <td class="px-4 py-3 text-slate-600 hidden md:table-cell text-xs">
                            {{ $p->email ?? '—' }}
                        </td>

                        {{-- Conselho --}}
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($p->conselho_profissional)
                                <span class="badge badge-blue">{{ $p->conselho_profissional }}</span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- Celular --}}
                        <td class="px-4 py-3 text-slate-500 hidden xl:table-cell text-xs">
                            {{ $p->celular1 ?: $p->telefone ?: '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @if($p->ativo)
                                <span class="badge badge-green">Ativo</span>
                            @else
                                <span class="badge badge-gray">Inativo</span>
                            @endif
                        </td>

                        {{-- Ações --}}
                        <td class="pr-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($whatsUrl)
                                    <a href="{{ $whatsUrl }}" target="_blank" title="WhatsApp"
                                       class="w-7 h-7 rounded-lg hover:bg-green-50 flex items-center justify-center text-slate-400 hover:text-green-500 transition-colors">
                                        <x-lucide-message-circle class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                                <a href="{{ route('profissionais.detalhes', $p->id) }}" title="Ver detalhes"
                                   class="w-7 h-7 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('profissionais.editar', $p->id) }}" title="Editar"
                                   class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors">
                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                </a>
                                @if($p->ativo)
                                <button type="button"
                                    x-data
                                    @click="$store.modal.confirm({
                                        type: 'warning',
                                        title: 'Inativar profissional',
                                        message: 'Deseja inativar {{ $p->nome }}?',
                                        confirmText: 'Inativar',
                                        onConfirm: () => $wire.inativarSelecionados([{{ $p->id }}])
                                    })"
                                    title="Inativar"
                                    class="w-7 h-7 rounded-lg hover:bg-amber-50 flex items-center justify-center text-slate-400 hover:text-amber-500 transition-colors">
                                    <x-lucide-user-x class="w-3.5 h-3.5" />
                                </button>
                                @else
                                <button type="button"
                                    x-data
                                    @click="$store.modal.confirm({
                                        type: 'info',
                                        title: 'Ativar profissional',
                                        message: 'Deseja ativar {{ $p->nome }}?',
                                        confirmText: 'Ativar',
                                        onConfirm: () => $wire.ativar({{ $p->id }})
                                    })"
                                    title="Ativar"
                                    class="w-7 h-7 rounded-lg hover:bg-emerald-50 flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-colors">
                                    <x-lucide-user-check class="w-3.5 h-3.5" />
                                </button>
                                @endif
                                <button type="button"
                                    x-data
                                    @click="$store.modal.confirm({
                                        type: 'delete',
                                        title: 'Excluir profissional',
                                        message: 'Deseja excluir {{ $p->nome }}? Esta ação não poderá ser desfeita.',
                                        confirmText: 'Excluir',
                                        onConfirm: () => $wire.excluir({{ $p->id }})
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
                            <td colspan="8" class="px-5 py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-1">
                                        <x-lucide-users class="w-5 h-5 text-slate-300" />
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">Nenhum profissional encontrado</p>
                                    <p class="text-xs text-slate-400">
                                        @if($busca || $this->temFiltrosAtivos()) Tente ajustar os filtros. @else Cadastre o primeiro profissional da equipe. @endif
                                    </p>
                                    @if(!$busca && !$this->temFiltrosAtivos())
                                        <a href="{{ route('profissionais.cadastro') }}" class="btn btn-primary text-xs mt-2 inline-flex gap-1.5">
                                            <x-lucide-plus class="w-3.5 h-3.5" /> Novo Profissional
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Rodapé --}}
        @if($profissionais->total() > 0)
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-xs text-slate-400">
                {{ $profissionais->firstItem() }}–{{ $profissionais->lastItem() }} de {{ $profissionais->total() }} profissionais
                @if(count($selecionados) > 0)
                    <span class="ml-2 text-blue-500 font-medium">· {{ count($selecionados) }} selecionado(s)</span>
                @endif
            </p>
            <x-pagination :paginator="$profissionais" />
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         VISUALIZAÇÃO CARDS
         ══════════════════════════════════════════════════════ --}}
    @if($visualizacao === 'cards')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($profissionais as $p)
        @php
            $inicial      = mb_strtoupper(mb_substr($p->nome, 0, 1));
            $cor          = $avatarColors[$inicial] ?? ['bg'=>'#dbeafe','text'=>'#1d4ed8'];
            $whatsNum     = preg_replace('/\D/', '', $p->celular1 ?: $p->telefone ?: '');
            $whatsUrl     = $whatsNum ? 'https://wa.me/55'.$whatsNum : null;
            $isSelecionado = in_array($p->id, $selecionados);
            $especialidade = $p->especialidades->first()?->nome;
        @endphp

        <div class="bg-white border rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col {{ $isSelecionado ? 'border-blue-300 ring-2 ring-blue-100' : 'border-slate-100' }}">

            {{-- Hero --}}
            <div class="relative flex-shrink-0 h-[110px]" style="background: linear-gradient(145deg, {{ $cor['bg'] }} 0%, {{ $cor['bg'] }}bb 100%)">

                {{-- Checkbox --}}
                <label class="absolute top-3 left-3 z-10 cursor-pointer">
                    <input type="checkbox"
                        x-data
                        wire:click="toggleSelecionado({{ $p->id }})"
                        :checked="$wire.selecionados.includes({{ $p->id }})"
                        class="w-4 h-4 rounded border-white/80 text-blue-600 cursor-pointer shadow-sm"
                        style="background: rgba(255,255,255,0.85)" />
                </label>

                {{-- Status --}}
                <div class="absolute top-3 right-3 z-10">
                    @if($p->ativo)
                        <span class="badge badge-green">Ativo</span>
                    @else
                        <span class="badge badge-gray">Inativo</span>
                    @endif
                </div>

                {{-- Círculos decorativos --}}
                <div class="absolute inset-0 overflow-hidden rounded-t-2xl pointer-events-none">
                    <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full opacity-20" style="background:{{ $cor['text'] }}"></div>
                    <div class="absolute -top-6 -left-6 w-20 h-20 rounded-full opacity-10" style="background:{{ $cor['text'] }}"></div>
                </div>

                {{-- Avatar --}}
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 z-10">
                    <div class="w-16 h-16 rounded-2xl border-[3px] border-white shadow-lg flex items-center justify-center overflow-hidden text-xl font-bold"
                         style="background:{{ $cor['bg'] }};color:{{ $cor['text'] }}">
                        @if($p->foto)
                            <img src="{{ Storage::url($p->foto) }}" class="w-full h-full object-cover" alt="" />
                        @else
                            {{ $inicial }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Corpo --}}
            <div class="px-4 pt-10 pb-3 flex-1 flex flex-col">

                {{-- Nome + código --}}
                <div class="mb-3 text-center">
                    <a href="{{ route('profissionais.detalhes', $p->id) }}"
                       class="font-bold text-slate-800 hover:text-blue-600 transition-colors leading-snug block truncate text-sm">
                        {{ $p->nome }}
                    </a>
                    <div class="flex items-center justify-center gap-1.5 mt-0.5">
                        @if($p->identificacao)
                            <span class="text-[11px] text-slate-400">{{ $p->identificacao }}</span>
                        @endif
                    </div>
                </div>

                {{-- Chips --}}
                <div class="flex flex-wrap justify-center gap-1.5 mb-3">
                    @if($especialidade)
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            <x-lucide-award class="w-2.5 h-2.5" />
                            {{ $especialidade }}
                        </span>
                    @endif
                    @if($p->conselho_profissional)
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600">
                            {{ $p->conselho_profissional }}
                        </span>
                    @endif
                </div>

                {{-- Contato --}}
                <div class="space-y-1.5 mb-3">
                    @if($p->celular1 ?: $p->telefone)
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <div class="w-5 h-5 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <x-lucide-phone class="w-3 h-3 text-slate-400" />
                            </div>
                            <span>{{ $p->celular1 ?: $p->telefone }}</span>
                        </div>
                    @endif
                    @if($p->email)
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <div class="w-5 h-5 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <x-lucide-mail class="w-3 h-3 text-slate-400" />
                            </div>
                            <span class="truncate">{{ $p->email }}</span>
                        </div>
                    @endif
                </div>

                {{-- Grid CPF / Unidade --}}
                <div class="mt-auto rounded-xl bg-slate-50 grid grid-cols-2 divide-x divide-slate-100 overflow-hidden">
                    <div class="px-3 py-2.5">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">CPF</div>
                        <div class="text-xs text-slate-600 leading-none">
                            {{ $p->cpf ? \Str::mask($p->cpf, '*', 3, 6) : '—' }}
                        </div>
                    </div>
                    <div class="px-3 py-2.5">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Unidade</div>
                        <div class="text-xs text-slate-600 leading-none truncate">
                            {{ $p->unidades->first()?->unidade ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rodapé de ações --}}
            <div class="border-t border-slate-100 px-3 py-2.5 flex items-center gap-2">
                @if($whatsUrl)
                    <a href="{{ $whatsUrl }}" target="_blank" title="Abrir WhatsApp"
                       class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-semibold transition-colors flex-shrink-0">
                        <x-lucide-message-circle class="w-3.5 h-3.5" />
                        WhatsApp
                    </a>
                @endif

                <a href="{{ route('profissionais.detalhes', $p->id) }}"
                   class="flex-1 text-center py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    Ver perfil
                </a>

                <a href="{{ route('profissionais.editar', $p->id) }}" title="Editar"
                   class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors flex-shrink-0">
                    <x-lucide-pencil class="w-3.5 h-3.5" />
                </a>

                @if($p->ativo)
                <button type="button" x-data
                    @click="$store.modal.confirm({
                        type: 'warning',
                        title: 'Inativar profissional',
                        message: 'Deseja inativar {{ $p->nome }}?',
                        confirmText: 'Inativar',
                        onConfirm: () => $wire.inativarSelecionados([{{ $p->id }}])
                    })"
                    title="Inativar"
                    class="w-7 h-7 rounded-lg hover:bg-amber-50 flex items-center justify-center text-slate-400 hover:text-amber-500 transition-colors flex-shrink-0">
                    <x-lucide-user-x class="w-3.5 h-3.5" />
                </button>
                @else
                <button type="button" x-data
                    @click="$store.modal.confirm({
                        type: 'info',
                        title: 'Ativar profissional',
                        message: 'Deseja ativar {{ $p->nome }}?',
                        confirmText: 'Ativar',
                        onConfirm: () => $wire.ativar({{ $p->id }})
                    })"
                    title="Ativar"
                    class="w-7 h-7 rounded-lg hover:bg-emerald-50 flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-colors flex-shrink-0">
                    <x-lucide-user-check class="w-3.5 h-3.5" />
                </button>
                @endif

                <button type="button" x-data
                    @click="$store.modal.confirm({
                        type: 'delete',
                        title: 'Excluir profissional',
                        message: 'Deseja excluir {{ $p->nome }}? Esta ação não poderá ser desfeita.',
                        confirmText: 'Excluir',
                        onConfirm: () => $wire.excluir({{ $p->id }})
                    })"
                    title="Excluir"
                    class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors flex-shrink-0">
                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>

        @empty
            <div class="col-span-full py-14 text-center">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-1">
                        <x-lucide-users class="w-5 h-5 text-slate-300" />
                    </div>
                    <p class="text-sm font-medium text-slate-500">Nenhum profissional encontrado</p>
                    <p class="text-xs text-slate-400">
                        @if($busca || $this->temFiltrosAtivos()) Tente ajustar os filtros. @else Cadastre o primeiro profissional da equipe. @endif
                    </p>
                    @if(!$busca && !$this->temFiltrosAtivos())
                        <a href="{{ route('profissionais.cadastro') }}" class="btn btn-primary text-xs mt-2 inline-flex gap-1.5">
                            <x-lucide-plus class="w-3.5 h-3.5" /> Novo Profissional
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginação cards --}}
    @if($profissionais->total() > 0)
    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400">
            {{ $profissionais->firstItem() }}–{{ $profissionais->lastItem() }} de {{ $profissionais->total() }} profissionais
        </p>
        <x-pagination :paginator="$profissionais" />
    </div>
    @endif
    @endif

</div>
