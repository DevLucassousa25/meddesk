<div
    x-data
    class="font-['Inter',system-ui,sans-serif]"
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

        $hoje = now();
        $mesAtual = $hoje->month;
    @endphp


    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Pacientes</h1>
            <p class="text-sm text-slate-400 mt-0.5">Gerencie os cadastros de pacientes da clínica</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Toggle visualização --}}
            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden h-10 bg-white">
                <button
                    wire:click="$set('visualizacao', 'tabela')"
                    type="button"
                    title="Visualização em tabela"
                    class="w-10 h-10 flex items-center justify-center transition-colors {{ $visualizacao === 'tabela' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}"
                ><x-lucide-list class="w-4 h-4" /></button>
                <button
                    wire:click="$set('visualizacao', 'cards')"
                    type="button"
                    title="Visualização em cards"
                    class="w-10 h-10 flex items-center justify-center transition-colors {{ $visualizacao === 'cards' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50' }}"
                ><x-lucide-layout-grid class="w-4 h-4" /></button>
            </div>

            {{-- Importar --}}
            <button
                wire:click="abrirImportacao"
                type="button"
                class="h-10 px-4 text-sm font-medium rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2"
            >
                <x-lucide-upload class="w-4 h-4" />
                Importar
            </button>

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

            {{-- Novo Paciente --}}
            <button wire:click="navegaFormularioCadastro" type="button" class="btn btn-primary gap-2 h-10 px-5 text-sm rounded-xl shadow-sm shadow-blue-200">
                <x-lucide-user-plus class="w-4 h-4" />
                Novo Paciente
            </button>
        </div>
    </div>

    {{-- ── Cards de resumo ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <x-lucide-users class="w-4 h-4 text-blue-500" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Total</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">pacientes cadastrados</div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <x-lucide-user-check class="w-4 h-4 text-emerald-500" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Ativos</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['ativos']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">em acompanhamento</div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center">
                    <x-lucide-user-x class="w-4 h-4 text-slate-400" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Inativos</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['inativos']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">sem atendimento ativo</div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
                    <x-lucide-alert-triangle class="w-4 h-4 text-red-400" />
                </div>
                <span class="text-[10px] font-700 uppercase tracking-widest text-slate-400">Inadimp.</span>
            </div>
            <div class="text-2xl font-bold text-slate-800">{{ number_format($stats['inadimplentes']) }}</div>
            <div class="text-xs text-slate-400 mt-0.5">com pendência financeira</div>
        </div>
    </div>

    {{-- ── Barra de filtros ─────────────────────────────────────────────────── --}}
    <div x-data="{ expanded: false }" class="bg-white border border-slate-100 rounded-xl mb-4">

        {{-- Linha 1: busca + status + ações --}}
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
                        class="px-3 h-7 rounded-lg text-xs font-medium transition-colors whitespace-nowrap
                            {{ $filtroStatus === $val ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100' }}"
                    >{{ $lbl }}</button>
                @endforeach
            </div>

            {{-- Botão filtros avançados --}}
            <div class="flex items-center h-11 px-3 gap-2 flex-shrink-0">
                <button
                    type="button"
                    @click="expanded = !expanded"
                    class="flex items-center gap-1.5 px-3 h-7 rounded-lg text-xs font-medium transition-colors"
                    :class="expanded ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-100'"
                >
                    <x-lucide-sliders-horizontal class="w-3.5 h-3.5" />
                    Filtros
                    @if($this->temFiltrosAtivos())
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

        {{-- Linha 2: filtros avançados (colapsável) --}}
        <div
            x-show="expanded"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="border-t border-slate-100 px-4 py-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 relative z-30"
            style="display:none"
        >
            @php
                $pal = ['#3b82f6','#22c55e','#f59e0b','#ec4899','#0d9488','#8b5cf6','#ef4444','#f97316','#06b6d4','#84cc16'];

                $optsConvenio = array_merge(
                    [['value'=>'','label'=>'Todos os convênios','color'=>'#94a3b8']],
                    collect($convenios)->values()->map(fn($c,$i)=>['value'=>(string)$c['id'],'label'=>$c['nome'],'color'=>$pal[$i%count($pal)]])->toArray()
                );

                $optsProfissional = array_merge(
                    [['value'=>'','label'=>'Todos os profissionais','color'=>'#94a3b8']],
                    collect($profissionais)->values()->map(fn($p,$i)=>['value'=>(string)$p['id'],'label'=>$p['nome'],'color'=>$pal[$i%count($pal)]])->toArray()
                );

                $optsGenero = [
                    ['value'=>'',            'label'=>'Todos os gêneros', 'color'=>'#94a3b8'],
                    ['value'=>'masculino',   'label'=>'Masculino',        'color'=>'#3b82f6'],
                    ['value'=>'feminino',    'label'=>'Feminino',         'color'=>'#ec4899'],
                    ['value'=>'outro',       'label'=>'Outro',            'color'=>'#8b5cf6'],
                    ['value'=>'nao_informado','label'=>'Não informado',   'color'=>'#cbd5e1'],
                ];

                $optsInad = [
                    ['value'=>'', 'label'=>'Todos',               'color'=>'#94a3b8'],
                    ['value'=>'1','label'=>'Apenas inadimplentes', 'color'=>'#ef4444'],
                ];

                $optsTipoVinculo = array_merge(
                    [['value'=>'','label'=>'Todos os vínculos','color'=>'#94a3b8']],
                    collect($tiposVinculo)->values()->map(fn($t,$i)=>['value'=>(string)$t['id'],'label'=>$t['nome'],'color'=>$pal[$i%count($pal)]])->toArray()
                );

                $optsFaixaEtaria = [
                    ['value'=>'',     'label'=>'Todas as idades', 'color'=>'#94a3b8'],
                    ['value'=>'0-17', 'label'=>'0–17 anos',       'color'=>'#06b6d4'],
                    ['value'=>'18-35','label'=>'18–35 anos',      'color'=>'#22c55e'],
                    ['value'=>'36-60','label'=>'36–60 anos',      'color'=>'#f59e0b'],
                    ['value'=>'60+',  'label'=>'60+ anos',        'color'=>'#8b5cf6'],
                ];

                $optsSemConsulta = [
                    ['value'=>'',    'label'=>'Sem filtro',        'color'=>'#94a3b8'],
                    ['value'=>'30',  'label'=>'Há +30 dias',       'color'=>'#f59e0b'],
                    ['value'=>'60',  'label'=>'Há +60 dias',       'color'=>'#f97316'],
                    ['value'=>'90',  'label'=>'Há +90 dias',       'color'=>'#ef4444'],
                    ['value'=>'180', 'label'=>'Há +180 dias',      'color'=>'#7f1d1d'],
                ];
            @endphp

            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Convênio</label>
                <x-select-dots wire="filtroConvenio" :options="$optsConvenio" placeholder="Todos os convênios" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Profissional</label>
                <x-select-dots wire="filtroProfissional" :options="$optsProfissional" placeholder="Todos os profissionais" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Vínculo</label>
                <x-select-dots wire="filtroTipoVinculo" :options="$optsTipoVinculo" placeholder="Todos os vínculos" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Gênero</label>
                <x-select-dots wire="filtroGenero" :options="$optsGenero" placeholder="Todos os gêneros" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Inadimplência</label>
                <x-select-dots wire="filtroInadimplente" :options="$optsInad" placeholder="Todos" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Faixa Etária</label>
                <x-select-dots wire="filtroFaixaEtaria" :options="$optsFaixaEtaria" placeholder="Todas as idades" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Sem Consulta Há</label>
                <x-select-dots wire="filtroSemConsulta" :options="$optsSemConsulta" placeholder="Sem filtro" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Cadastrado de</label>
                <x-datepicker wire="filtroDe" placeholder="dd/mm/aaaa" />
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Cadastrado até</label>
                <x-datepicker wire="filtroAte" placeholder="dd/mm/aaaa" />
            </div>

            {{-- Aniversariantes toggle --}}
            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors w-full">
                    <input wire:model.live="filtroAniversariantes" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 flex-shrink-0" />
                    <div>
                        <span class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                            <x-lucide-cake class="w-4 h-4" /> Aniversariantes
                        </span>
                        <span class="text-[10px] text-slate-400">deste mês</span>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- ── Chips de filtros ativos ──────────────────────────────────────────── --}}
    @php
        $chipsAtivos = [];
        if($busca)               $chipsAtivos[] = ['label'=>'"'.$busca.'"',                                                                                                              'clear'=>'busca',              'value'=>''];
        if($filtroStatus)        $chipsAtivos[] = ['label'=>['ativo'=>'Ativos','inativo'=>'Inativos'][$filtroStatus]??$filtroStatus,                                                     'clear'=>'filtroStatus',       'value'=>''];
        if($filtroConvenio)      $chipsAtivos[] = ['label'=>'Convênio: '.(collect($convenios)->firstWhere('id',(int)$filtroConvenio)['nome']??'—'),                                      'clear'=>'filtroConvenio',     'value'=>''];
        if($filtroProfissional)  $chipsAtivos[] = ['label'=>'Prof.: '.(collect($profissionais)->firstWhere('id',(int)$filtroProfissional)['nome']??'—'),                                 'clear'=>'filtroProfissional', 'value'=>''];
        if($filtroTipoVinculo)   $chipsAtivos[] = ['label'=>'Vínculo: '.(collect($tiposVinculo)->firstWhere('id',(int)$filtroTipoVinculo)['nome']??'—'),                                 'clear'=>'filtroTipoVinculo',  'value'=>''];
        if($filtroGenero)        $chipsAtivos[] = ['label'=>['masculino'=>'Masculino','feminino'=>'Feminino','outro'=>'Outro','nao_informado'=>'Não informado'][$filtroGenero]??$filtroGenero, 'clear'=>'filtroGenero', 'value'=>''];
        if($filtroInadimplente)  $chipsAtivos[] = ['label'=>'Inadimplentes',                                                                                                             'clear'=>'filtroInadimplente', 'value'=>''];
        if($filtroFaixaEtaria)   $chipsAtivos[] = ['label'=>'Faixa: '.['0-17'=>'0–17 anos','18-35'=>'18–35 anos','36-60'=>'36–60 anos','60+'=>'60+ anos'][$filtroFaixaEtaria],            'clear'=>'filtroFaixaEtaria',  'value'=>''];
        if($filtroSemConsulta)   $chipsAtivos[] = ['label'=>'Sem consulta há +'.$filtroSemConsulta.' dias',                                                                              'clear'=>'filtroSemConsulta',  'value'=>''];
        if($filtroAniversariantes) $chipsAtivos[] = ['label'=>'Aniversariantes deste mês',                                                                                            'clear'=>'filtroAniversariantes','value'=>false];
        if($filtroDe)            $chipsAtivos[] = ['label'=>'De: '.\Carbon\Carbon::parse($filtroDe)->format('d/m/Y'),                                                                    'clear'=>'filtroDe',           'value'=>''];
        if($filtroAte)           $chipsAtivos[] = ['label'=>'Até: '.\Carbon\Carbon::parse($filtroAte)->format('d/m/Y'),                                                                  'clear'=>'filtroAte',          'value'=>''];
    @endphp
    @if(count($chipsAtivos))
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Filtros ativos:</span>
        @foreach($chipsAtivos as $chip)
            <button
                wire:click="$set('{{ $chip['clear'] }}', {{ is_bool($chip['value']) ? ($chip['value'] ? 'true' : 'false') : "'" . $chip['value'] . "'" }})"
                type="button"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-colors group"
            >
                {{ $chip['label'] }}
                <x-lucide-x class="w-3 h-3 opacity-40 group-hover:opacity-100" />
            </button>
        @endforeach
        @if(count($chipsAtivos) > 1)
            <button wire:click="limparFiltros" type="button" class="text-xs text-slate-400 hover:text-red-400 transition-colors font-medium">
                Limpar todos
            </button>
        @endif
    </div>
    @endif

    {{-- ── Bulk action bar ─────────────────────────────────────────────────── --}}
    @if(count($selecionados) > 0)
    <div class="flex items-center gap-3 px-4 py-2.5 bg-blue-600 text-white rounded-xl mb-3 text-sm">
        <div class="flex items-center gap-2">
            <div class="w-5 h-5 rounded bg-white/20 flex items-center justify-center">
                <x-lucide-check class="w-3 h-3" />
            </div>
            <span class="font-semibold">{{ count($selecionados) }} selecionado(s)</span>
        </div>
        <div class="flex items-center gap-2 ml-auto flex-wrap">
            <button
                wire:click="exportarSelecionados"
                wire:loading.attr="disabled"
                type="button"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors"
            >
                <svg wire:loading wire:target="exportarSelecionados" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <x-lucide-download wire:loading.remove wire:target="exportarSelecionados" class="w-3.5 h-3.5" />
                <span wire:loading wire:target="exportarSelecionados">Exportando...</span>
                <span wire:loading.remove wire:target="exportarSelecionados">Exportar</span>
            </button>
            @if($this->selecionadosTodosInativos)
            <button x-data
                @click="$store.modal.confirm({
                    type: 'info',
                    title: 'Ativar pacientes',
                    message: 'Deseja ativar os {{ count($selecionados) }} paciente(s) selecionado(s)?',
                    confirmText: 'Ativar',
                    onConfirm: () => $wire.ativarSelecionados()
                })"
                wire:loading.attr="disabled" type="button"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-colors">
                <x-lucide-user-check class="w-3.5 h-3.5" /> Ativar
            </button>
            @else
            <button x-data
                @click="$store.modal.confirm({
                    type: 'warning',
                    title: 'Inativar pacientes',
                    message: 'Deseja inativar os {{ count($selecionados) }} paciente(s) selecionado(s)?',
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
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         VISUALIZAÇÃO TABELA
         ══════════════════════════════════════════════════════════════════════ --}}
    @if($visualizacao === 'tabela')
    @php
        $idsVisiveis = $pacientes->pluck('id')->toArray();
        $todosVisiveis = count($idsVisiveis) > 0 && count(array_intersect($idsVisiveis, $selecionados)) === count($idsVisiveis);
    @endphp
    <div class="bg-white border border-slate-100 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        {{-- Checkbox select all --}}
                        <th class="pl-4 pr-2 py-3 w-8">
                            <input
                                type="checkbox"
                                x-data
                                wire:click="selecionarTodosVisiveis({{ json_encode($idsVisiveis) }})"
                                :checked="{{ json_encode($todosVisiveis) }}"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer"
                            />
                        </th>

                        {{-- Paciente (sortável) --}}
                        <th class="text-left px-3 py-3">
                            <button wire:click="sortarPor('nome')" type="button" class="flex items-center gap-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-600 transition-colors">
                                Paciente
                                @if($sortBy === 'nome')
                                    <x-lucide-chevron-up class="w-3 h-3 {{ $sortDir === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                @else
                                    <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" />
                                @endif
                            </button>
                        </th>

                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">CPF</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Convênio</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Profissional</th>

                        {{-- Última consulta (sortável) --}}
                        <th class="text-left px-4 py-3 hidden xl:table-cell">
                            <button wire:click="sortarPor('ultima_consulta')" type="button" class="flex items-center gap-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-600 transition-colors">
                                Última Consulta
                                @if($sortBy === 'ultima_consulta')
                                    <x-lucide-chevron-up class="w-3 h-3 {{ $sortDir === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                @else
                                    <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" />
                                @endif
                            </button>
                        </th>

                        {{-- Próxima consulta --}}
                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden xl:table-cell">Próx. Consulta</th>

                        <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>

                        {{-- Data cadastro (sortável) --}}
                        <th class="text-left px-4 py-3 hidden 2xl:table-cell">
                            <button wire:click="sortarPor('created_at')" type="button" class="flex items-center gap-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-600 transition-colors">
                                Cadastro
                                @if($sortBy === 'created_at')
                                    <x-lucide-chevron-up class="w-3 h-3 {{ $sortDir === 'desc' ? 'rotate-180' : '' }} transition-transform" />
                                @else
                                    <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" />
                                @endif
                            </button>
                        </th>

                        <th class="py-3 pr-4 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($pacientes as $paciente)
                        @php
                            $inicial = mb_strtoupper(mb_substr($paciente->nome, 0, 1));
                            $cor     = $avatarColors[$inicial] ?? ['bg'=>'#dbeafe','text'=>'#1d4ed8'];

                            $ehAniversarioHoje = $paciente->data_nascimento
                                && $paciente->data_nascimento->format('m-d') === $hoje->format('m-d');
                            $ehAniversarioSemana = !$ehAniversarioHoje && $paciente->data_nascimento
                                && \Carbon\Carbon::createFromFormat('Y-m-d',
                                    $hoje->year.'-'.$paciente->data_nascimento->format('m-d')
                                )->between($hoje, $hoje->copy()->addDays(7));

                            $ultimaConsulta   = $paciente->ultima_consulta
                                ? \Carbon\Carbon::parse($paciente->ultima_consulta) : null;
                            $proximaConsulta  = $paciente->proxima_consulta
                                ? \Carbon\Carbon::parse($paciente->proxima_consulta) : null;

                            $isSelecionado = in_array($paciente->id, $selecionados);

                            // WhatsApp
                            $whatsNum = preg_replace('/\D/', '', $paciente->celular1 ?: $paciente->telefone1 ?: '');
                            $whatsUrl = $whatsNum ? 'https://wa.me/55'.$whatsNum : null;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors group {{ $isSelecionado ? 'bg-blue-50/50' : '' }}">

                            {{-- Checkbox --}}
                            <td class="pl-4 pr-2 py-3">
                                <input
                                    type="checkbox"
                                    x-data
                                    wire:click="toggleSelecionado({{ $paciente->id }})"
                                    :checked="$wire.selecionados.includes({{ $paciente->id }})"
                                    class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer"
                                />
                            </td>

                            {{-- Paciente --}}
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden text-xs font-bold"
                                            style="background:{{ $cor['bg'] }};color:{{ $cor['text'] }}"
                                        >
                                            @if ($paciente->foto)
                                                <img src="{{ Storage::url($paciente->foto) }}" class="w-full h-full object-cover" alt="" />
                                            @else
                                                {{ $inicial }}
                                            @endif
                                        </div>
                                        @if($ehAniversarioHoje)
                                            <span title="Aniversário hoje!" class="absolute -top-1 -right-1 text-[10px]">
                                                <x-lucide-cake class="w-3 h-3 text-red-500" />
                                            </span>
                                        @elseif($ehAniversarioSemana)
                                            <span title="Aniversário esta semana" class="absolute -top-1 -right-1 text-[10px]">
                                                <x-lucide-gift class="w-3 h-3 text-yellow-500" />
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('pacientes.detalhes', $paciente->id) }}" class="font-medium text-slate-800 hover:text-blue-600 transition-colors leading-tight truncate block max-w-[160px]">{{ $paciente->nome }}</a>
                                        <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                            {{ $paciente->codigo }}
                                            @if($paciente->data_nascimento)
                                                <span class="text-slate-300">·</span>
                                                <span>{{ $paciente->data_nascimento->age }} anos</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- CPF --}}
                            <td class="px-4 py-3 text-slate-500 hidden sm:table-cell text-xs">
                                {{ $paciente->cpf ? \Str::mask($paciente->cpf, '*', 3, 6) : '—' }}
                            </td>

                            {{-- Convênio --}}
                            <td class="px-4 py-3 text-slate-600 hidden md:table-cell text-xs">
                                {{ $paciente->convenio?->nome ?? '—' }}
                            </td>

                            {{-- Profissional --}}
                            <td class="px-4 py-3 text-slate-600 hidden lg:table-cell text-xs">
                                {{ $paciente->profissionalResponsavel?->nome ?? '—' }}
                            </td>

                            {{-- Última consulta --}}
                            <td class="px-4 py-3 hidden xl:table-cell">
                                @if($ultimaConsulta)
                                    <div class="text-xs text-slate-600">{{ $ultimaConsulta->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $ultimaConsulta->diffForHumans() }}</div>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Próxima consulta --}}
                            <td class="px-4 py-3 hidden xl:table-cell">
                                @if($proximaConsulta)
                                    <div class="text-xs font-medium text-emerald-600">{{ $proximaConsulta->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $proximaConsulta->format('H:i') }}</div>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1 items-center justify-center">
                                    @if ($paciente->ativo)
                                        <span class="badge badge-green">Ativo</span>
                                    @else
                                        <span class="badge badge-gray">Inativo</span>
                                    @endif
                                    @if ($paciente->inadimplente)
                                        <span class="badge badge-red">Inad.</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Cadastro --}}
                            <td class="px-4 py-3 text-xs text-slate-400 hidden 2xl:table-cell">
                                {{ $paciente->created_at->format('d/m/Y') }}
                            </td>

                            {{-- Ações --}}
                            <td class="pr-4 py-3">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- WhatsApp --}}
                                    @if($whatsUrl)
                                        <a
                                            href="{{ $whatsUrl }}"
                                            target="_blank"
                                            title="WhatsApp"
                                            class="w-7 h-7 rounded-lg hover:bg-green-50 flex items-center justify-center text-slate-400 hover:text-green-500 transition-colors"
                                        ><x-lucide-message-circle class="w-3.5 h-3.5" /></a>
                                    @endif

                                    {{-- Ver detalhes --}}
                                    <a
                                        href="{{ route('pacientes.detalhes', $paciente->id) }}"
                                        title="Ver detalhes"
                                        class="w-7 h-7 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors"
                                    ><x-lucide-eye class="w-3.5 h-3.5" /></a>

                                    {{-- Editar --}}
                                    <a
                                        href="{{ route('pacientes.editar', $paciente->id) }}"
                                        title="Editar"
                                        class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors"
                                    ><x-lucide-pencil class="w-3.5 h-3.5" /></a>

                                    {{-- Excluir --}}
                                    <button type="button" x-data
                                        @click="$store.modal.confirm({
                                            type: 'delete',
                                            title: 'Excluir paciente',
                                            message: 'Deseja excluir {{ $paciente->nome }}? Esta ação não poderá ser desfeita.',
                                            confirmText: 'Excluir',
                                            onConfirm: () => $wire.excluir({{ $paciente->id }})
                                        })"
                                        title="Excluir"
                                        class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400 transition-colors"
                                    ><x-lucide-trash-2 class="w-3.5 h-3.5" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-5 py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-1">
                                        <x-lucide-users class="w-5 h-5 text-slate-300" />
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">Nenhum paciente encontrado</p>
                                    <p class="text-xs text-slate-400">
                                        @if($busca || $this->temFiltrosAtivos()) Tente ajustar os filtros. @else Cadastre o primeiro paciente. @endif
                                    </p>
                                    @if(!$busca && !$this->temFiltrosAtivos())
                                        <button wire:click="navegaFormularioCadastro" type="button" class="btn btn-primary text-xs mt-2">
                                            <x-lucide-plus class="w-3.5 h-3.5" /> Novo Paciente
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Rodapé --}}
        @if($pacientes->total() > 0)
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-xs text-slate-400">
                {{ $pacientes->firstItem() }}–{{ $pacientes->lastItem() }} de {{ $pacientes->total() }} pacientes
                @if(count($selecionados) > 0)
                    <span class="ml-2 text-blue-500 font-medium">· {{ count($selecionados) }} selecionado(s)</span>
                @endif
            </p>
            <x-pagination :paginator="$pacientes" />
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         VISUALIZAÇÃO CARDS
         ══════════════════════════════════════════════════════════════════════ --}}
    @if($visualizacao === 'cards')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($pacientes as $paciente)
            @php
                $inicial = mb_strtoupper(mb_substr($paciente->nome, 0, 1));
                $cor     = $avatarColors[$inicial] ?? ['bg'=>'#dbeafe','text'=>'#1d4ed8'];

                $ehAniversarioHoje = $paciente->data_nascimento
                    && $paciente->data_nascimento->format('m-d') === $hoje->format('m-d');
                $ehAniversarioSemana = !$ehAniversarioHoje && $paciente->data_nascimento
                    && \Carbon\Carbon::createFromFormat('Y-m-d',
                        $hoje->year.'-'.$paciente->data_nascimento->format('m-d')
                    )->between($hoje, $hoje->copy()->addDays(7));

                $ultimaConsulta  = $paciente->ultima_consulta  ? \Carbon\Carbon::parse($paciente->ultima_consulta)  : null;
                $proximaConsulta = $paciente->proxima_consulta ? \Carbon\Carbon::parse($paciente->proxima_consulta) : null;
                $isSelecionado   = in_array($paciente->id, $selecionados);
                $whatsNum = preg_replace('/\D/', '', $paciente->celular1 ?: $paciente->telefone1 ?: '');
                $whatsUrl = $whatsNum ? 'https://wa.me/55'.$whatsNum : null;
            @endphp

            <div class="bg-white border rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col {{ $isSelecionado ? 'border-blue-300 ring-2 ring-blue-100' : 'border-slate-100' }}">

                {{-- ── Hero do card ── --}}
                <div class="relative flex-shrink-0 h-[110px]" style="background: linear-gradient(145deg, {{ $cor['bg'] }} 0%, {{ $cor['bg'] }}bb 100%)">

                    {{-- Checkbox --}}
                    <label class="absolute top-3 left-3 z-10 cursor-pointer">
                        <input
                            type="checkbox"
                            x-data
                            wire:click="toggleSelecionado({{ $paciente->id }})"
                            :checked="$wire.selecionados.includes({{ $paciente->id }})"
                            class="w-4 h-4 rounded border-white/80 text-blue-600 cursor-pointer shadow-sm"
                            style="background: rgba(255,255,255,0.85)"
                        />
                    </label>

                    {{-- Status --}}
                    <div class="absolute top-3 right-3 z-10 flex items-center gap-1">
                        @if($paciente->inadimplente)
                            <span class="badge badge-red">Inad.</span>
                        @endif
                        @if($paciente->ativo)
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

                    {{-- Avatar centralizado na faixa --}}
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 z-10">
                        <div class="relative">
                            <div
                                class="w-16 h-16 rounded-2xl border-[3px] border-white shadow-lg flex items-center justify-center overflow-hidden text-xl font-bold"
                                style="background:{{ $cor['bg'] }};color:{{ $cor['text'] }}"
                            >
                                @if($paciente->foto)
                                    <img src="{{ Storage::url($paciente->foto) }}" class="w-full h-full object-cover" alt="" />
                                @else
                                    {{ $inicial }}
                                @endif
                            </div>
                            @if($ehAniversarioHoje)
                                <span title="Aniversário hoje!" class="absolute -top-1 -right-1 text-base leading-none"><x-lucide-cake class="w-4 h-4" /></span>
                            @elseif($ehAniversarioSemana)
                                <span title="Aniversário esta semana" class="absolute -top-1 -right-1 text-base leading-none"><x-lucide-gift class="w-4 h-4" /></span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Corpo do card ── --}}
                <div class="px-4 pt-10 pb-3 flex-1 flex flex-col">

                    {{-- Nome + código --}}
                    <div class="mb-3 text-center">
                        <a href="{{ route('pacientes.detalhes', $paciente->id) }}"
                           class="font-bold text-slate-800 hover:text-blue-600 transition-colors leading-snug block truncate text-sm">
                            {{ $paciente->nome }}
                        </a>
                        <div class="flex items-center justify-center gap-1.5 mt-0.5">
                            <span class="text-[11px] text-slate-400">{{ $paciente->codigo }}</span>
                            @if($paciente->data_nascimento)
                                <span class="text-slate-200">·</span>
                                <span class="text-[11px] text-slate-400">{{ $paciente->data_nascimento->age }} anos</span>
                            @endif
                            @if($paciente->genero === 'masculino')
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-400 font-medium">M</span>
                            @elseif($paciente->genero === 'feminino')
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-pink-50 text-pink-400 font-medium">F</span>
                            @endif
                        </div>
                    </div>

                    {{-- Chips de info --}}
                    <div class="flex flex-wrap justify-center gap-1.5 mb-3">
                        @if($paciente->convenio)
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                <x-lucide-shield class="w-2.5 h-2.5" />
                                {{ $paciente->convenio->nome }}
                            </span>
                        @endif
                        @if($paciente->tipoVinculo)
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                {{ $paciente->tipoVinculo->nome }}
                            </span>
                        @endif
                    </div>

                    {{-- Profissional + Telefone --}}
                    <div class="space-y-1.5 mb-3">
                        @if($paciente->profissionalResponsavel)
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <div class="w-5 h-5 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <x-lucide-stethoscope class="w-3 h-3 text-slate-400" />
                                </div>
                                <span class="truncate">{{ $paciente->profissionalResponsavel->nome }}</span>
                            </div>
                        @endif
                        @if($paciente->celular1 || $paciente->telefone1)
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <div class="w-5 h-5 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <x-lucide-phone class="w-3 h-3 text-slate-400" />
                                </div>
                                <span>{{ $paciente->celular1 ?: $paciente->telefone1 }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Consultas --}}
                    <div class="mt-auto rounded-xl bg-slate-50 grid grid-cols-2 divide-x divide-slate-100 overflow-hidden">
                        <div class="px-3 py-2.5">
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Última</div>
                            @if($ultimaConsulta)
                                <div class="text-xs font-semibold text-slate-700 leading-none">{{ $ultimaConsulta->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $ultimaConsulta->diffForHumans() }}</div>
                            @else
                                <div class="text-xs text-slate-300 leading-none">Sem registro</div>
                            @endif
                        </div>
                        <div class="px-3 py-2.5">
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Próxima</div>
                            @if($proximaConsulta)
                                <div class="text-xs font-semibold text-emerald-600 leading-none">{{ $proximaConsulta->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $proximaConsulta->format('H:i') }}</div>
                            @else
                                <div class="text-xs text-slate-300 leading-none">Não agendada</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Rodapé de ações ── --}}
                <div class="border-t border-slate-100 px-3 py-2.5 flex items-center gap-2">
                    {{-- WhatsApp --}}
                    @if($whatsUrl)
                        <a href="{{ $whatsUrl }}" target="_blank" title="Abrir WhatsApp"
                           class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-xs font-semibold transition-colors flex-shrink-0">
                            <x-lucide-message-circle class="w-3.5 h-3.5" />
                            WhatsApp
                        </a>
                    @endif

                    {{-- Ver perfil --}}
                    <a href="{{ route('pacientes.detalhes', $paciente->id) }}"
                       class="flex-1 text-center py-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                        Ver perfil
                    </a>

                    {{-- Editar --}}
                    <a href="{{ route('pacientes.editar', $paciente->id) }}" title="Editar"
                            class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500 transition-colors flex-shrink-0">
                        <x-lucide-pencil class="w-3.5 h-3.5" />
                    </a>

                    {{-- Excluir --}}
                    <button type="button" x-data
                            @click="$store.modal.confirm({
                                type: 'delete',
                                title: 'Excluir paciente',
                                message: 'Deseja excluir {{ $paciente->nome }}? Esta ação não poderá ser desfeita.',
                                confirmText: 'Excluir',
                                onConfirm: () => $wire.excluir({{ $paciente->id }})
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
                    <p class="text-sm font-medium text-slate-500">Nenhum paciente encontrado</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginação cards --}}
    @if($pacientes->total() > 0)
    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400">
            {{ $pacientes->firstItem() }}–{{ $pacientes->lastItem() }} de {{ $pacientes->total() }} pacientes
        </p>
        <x-pagination :paginator="$pacientes" />
    </div>
    @endif
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         MODAL IMPORTAÇÃO
         ════════════════════════════════════════════════════════════════════ --}}
    @if($modalImportacao)
    <div
        x-init="$nextTick(() => { document.body.style.overflow = 'hidden'; })"
        x-destroy="document.body.style.overflow = ''"
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 sm:p-6"
    >
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="fecharImportacao"></div>

        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl my-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        >
            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <x-lucide-upload class="w-4 h-4 text-white" />
                </div>
                <h2 class="text-base font-bold text-slate-800 flex-1">Importar Pacientes</h2>
                <button wire:click="fecharImportacao" type="button" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="p-5 space-y-4">
                @if(empty($importResultado))
                    {{-- Instruções --}}
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-700 space-y-1">
                        <p class="font-semibold">Formato esperado do CSV (separador: ponto-e-vírgula)</p>
                        <p class="font-mono text-[11px] bg-blue-100/60 rounded px-2 py-1 break-all">Nome;CPF;E-mail;Telefone;Celular;Data Nasc.;Gênero;Cidade;UF</p>
                        <ul class="list-disc list-inside text-blue-600 mt-1 space-y-0.5">
                            <li><strong>Nome</strong> é obrigatório</li>
                            <li>Data no formato <strong>dd/mm/aaaa</strong></li>
                            <li>Gênero: masculino, feminino, outro, nao_informado</li>
                        </ul>
                    </div>

                    {{-- Upload --}}
                    <div>
                        <label class="form-label">Arquivo CSV</label>
                        <input wire:model="arquivoImport" type="file" accept=".csv,.txt"
                               class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                        @error('arquivoImport') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="arquivoImport" class="text-xs text-slate-400 mt-1">Carregando arquivo...</div>
                    </div>
                @else
                    {{-- Resultado --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                            <x-lucide-check-circle class="w-5 h-5 text-emerald-500 flex-shrink-0" />
                            <div>
                                <p class="text-sm font-semibold text-emerald-700">{{ $importResultado['importados'] }} paciente(s) importado(s) com sucesso!</p>
                            </div>
                        </div>
                        @if(!empty($importResultado['erros']))
                            <div class="p-3 bg-red-50 border border-red-100 rounded-xl">
                                <p class="text-xs font-semibold text-red-600 mb-2">{{ count($importResultado['erros']) }} erro(s):</p>
                                <ul class="space-y-0.5 max-h-32 overflow-y-auto">
                                    @foreach($importResultado['erros'] as $erro)
                                        <li class="text-xs text-red-500">{{ $erro }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button wire:click="fecharImportacao" type="button" class="btn btn-secondary">
                    {{ empty($importResultado) ? 'Cancelar' : 'Fechar' }}
                </button>
                @if(empty($importResultado))
                    <button wire:click="processarImportacao" type="button" class="btn btn-primary" wire:loading.attr="disabled">
                        <div wire:loading wire:target="processarImportacao">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </div>
                        <x-lucide-upload wire:loading.remove wire:target="processarImportacao" class="w-4 h-4" />
                        Importar
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════
         MODAL DE CADASTRO / EDIÇÃO
         ════════════════════════════════════════════════════════════════════ --}}
    @if ($modalAberto)
    <div
        x-data="{ tab: 0, cnpj: false }"
        x-init="$nextTick(() => { document.body.style.overflow = 'hidden'; })"
        x-destroy="document.body.style.overflow = ''"
        class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 sm:p-6"
    >
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="fecharModal"></div>

        <div
            class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl my-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        >
            {{-- Modal Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <x-lucide-user-plus class="w-4 h-4 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-base font-bold text-slate-800">
                        {{ $pacienteId ? 'Editar Paciente' : 'Novo Paciente' }}
                    </h2>
                    @if($codigo)
                        <p class="text-xs text-slate-400">{{ $codigo }}</p>
                    @endif
                </div>
                <button wire:click="fecharModal" type="button" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-slate-100 px-5 gap-0.5">
                @foreach ([
                    [0, 'Dados Gerais',   'lucide-user'],
                    [1, 'Endereço',       'lucide-map-pin'],
                    [2, 'Contato',        'lucide-phone'],
                    [3, 'Clínico',        'lucide-stethoscope'],
                    [4, 'Financeiro',     'lucide-wallet'],
                ] as [$idx, $label, $icon])
                    <button
                        type="button"
                        @click="tab = {{ $idx }}"
                        :class="tab === {{ $idx }}
                            ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                            : 'text-slate-400 hover:text-slate-600'"
                        class="flex items-center gap-1.5 px-3 py-3 text-xs transition-colors whitespace-nowrap"
                    >
                        <x-dynamic-component :component="$icon" class="w-3.5 h-3.5" />
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Modal Body --}}
            <div class="p-5 max-h-[calc(100vh-260px)] overflow-y-auto space-y-4">

                @if ($errors->any())
                    <div class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">
                        <x-lucide-alert-circle class="w-4 h-4 flex-shrink-0 mt-0.5" />
                        <ul class="space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- TAB 0: Dados Gerais --}}
                <div x-show="tab === 0" class="space-y-4">
                    <div class="flex flex-wrap gap-4 items-start">
                        <div class="flex flex-col items-center gap-2">
                            <label for="foto-modal" class="w-20 h-20 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 flex items-center justify-center cursor-pointer hover:border-blue-400 transition-all overflow-hidden relative flex-shrink-0">
                                @if ($foto)
                                    <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover" />
                                @elseif ($foto_atual)
                                    <img src="{{ Storage::url($foto_atual) }}" class="w-full h-full object-cover" />
                                @else
                                    <x-lucide-user class="w-8 h-8 text-blue-300" />
                                @endif
                                <div wire:loading.flex wire:target="foto" class="absolute inset-0 bg-white/80 items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </div>
                            </label>
                            <input id="foto-modal" type="file" wire:model="foto" accept="image/*" class="hidden" />
                            @if($foto || $foto_atual)
                                <button wire:click="removerFoto" type="button" class="text-[11px] text-red-400 hover:text-red-600 transition-colors">Remover</button>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="form-label">Nome Completo <span class="text-red-400">*</span></label>
                                <input wire:model="nome" type="text" placeholder="Nome do paciente" class="form-input" />
                                @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Status</label>
                                @php
                                    $statusOpts = [
                                        ['value'=>'ativo',    'label'=>'Ativo',    'color'=>'#22c55e'],
                                        ['value'=>'inativo',  'label'=>'Inativo',  'color'=>'#94a3b8'],
                                        ['value'=>'prospect', 'label'=>'Prospect', 'color'=>'#f59e0b'],
                                    ];
                                @endphp
                                <x-select-dots wire="status_cliente" :options="$statusOpts" placeholder="Selecionar..." />
                            </div>
                            <div>
                                <label class="form-label">Gênero</label>
                                <x-select
                                    wire="genero"
                                    :options="[
                                        ['value' => 'masculino',    'label' => 'Masculino'],
                                        ['value' => 'feminino',     'label' => 'Feminino'],
                                        ['value' => 'outro',        'label' => 'Outro'],
                                        ['value' => 'nao_informado','label' => 'Prefiro não informar'],
                                    ]"
                                    placeholder="Selecionar..."
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">{{ $cnpj ? 'CNPJ' : 'CPF' }}</label>
                            <div class="flex gap-1.5">
                                <input wire:model="cpf"  x-show="!cnpj" type="text" placeholder="000.000.000-00"        class="form-input" />
                                <input wire:model="cnpj" x-show="cnpj"  type="text" placeholder="00.000.000/0000-00"    class="form-input" />
                                <button type="button" @click="cnpj = !cnpj" class="flex-shrink-0 px-2.5 h-[38px] bg-slate-100 hover:bg-slate-200 rounded-lg text-xs text-slate-500 font-medium transition-colors" x-text="cnpj ? 'CPF' : 'CNPJ'"></button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">RG</label>
                            <input wire:model="rg" type="text" placeholder="00.000.000-0" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Data de Nascimento
                                @if($idade) <span class="text-blue-500 font-normal">({{ $idade }} anos)</span> @endif
                            </label>
                            <input wire:model.live="data_nascimento" type="date" class="form-input" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Empresa</label>
                            <input wire:model="empresa" type="text" placeholder="Nome da empresa" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Ativo</label>
                            <div class="flex items-center gap-2 h-[38px]">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input wire:model="ativo" type="checkbox" class="sr-only peer" />
                                    <div class="w-9 h-5 bg-slate-200 peer-checked:bg-blue-600 rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-4"></div>
                                </label>
                                <span class="text-sm text-slate-600">{{ $ativo ? 'Ativo' : 'Inativo' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 1: Endereço --}}
                <div x-show="tab === 1" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">CEP</label>
                            <div class="flex gap-1.5">
                                <input wire:model="cep" type="text" placeholder="00000-000" class="form-input" @blur="$wire.buscarCep($event.target.value)" />
                                <button type="button" wire:click="buscarCep" class="flex-shrink-0 px-2.5 h-[38px] bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <div wire:loading wire:target="buscarCep" class="w-4 h-4">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                    </div>
                                    <x-lucide-search wire:loading.remove wire:target="buscarCep" class="w-4 h-4" />
                                </button>
                            </div>
                            @if($cep_erro)<p class="text-xs text-red-500 mt-1">{{ $cep_erro }}</p>@endif
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Endereço</label>
                            <input wire:model="endereco" type="text" placeholder="Rua, Av., etc." class="form-input" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="form-label">Número</label>
                            <input wire:model="numero" type="text" placeholder="Nº" class="form-input" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Complemento</label>
                            <input wire:model="complemento" type="text" placeholder="Apto, Sala..." class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">UF</label>
                            <input wire:model="uf" type="text" placeholder="SP" maxlength="2" class="form-input uppercase" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Bairro</label>
                            <input wire:model="bairro" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Cidade</label>
                            <input wire:model="cidade" type="text" class="form-input" />
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Contato --}}
                <div x-show="tab === 2" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">Telefone</label>
                            <input wire:model="telefone1" type="text" placeholder="(00) 0000-0000" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Celular 1</label>
                            <input wire:model="celular1" type="text" placeholder="(00) 00000-0000" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Celular 2</label>
                            <input wire:model="celular2" type="text" placeholder="(00) 00000-0000" class="form-input" />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">E-mail</label>
                        <input wire:model="email" type="email" placeholder="paciente@email.com" class="form-input" />
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="card p-4 space-y-3">
                        <p class="text-xs font-semibold text-slate-500">Configurações de Mensagem</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ([
                                ['enviar_whatsapp',    'Enviar WhatsApp'],
                                ['whatsapp_ativo',     'WhatsApp Ativo'],
                                ['sms_ativo',          'SMS Ativo'],
                                ['numero_internacional','Número Internacional'],
                            ] as [$prop, $label])
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input wire:model="{{ $prop }}" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600" />
                                    <span class="text-xs text-slate-600">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- TAB 3: Clínico --}}
                <div x-show="tab === 3" class="space-y-3">
                    @php
                        $palette = ['#3b82f6','#22c55e','#2563eb','#f59e0b','#ec4899','#0d9488','#8b5cf6'];
                        $tiposOpts = collect($tiposVinculo)->values()->map(fn($t,$i) => ['value' => (string)$t['id'], 'label' => $t['nome'], 'color' => $palette[$i % count($palette)]])->toArray();
                        $profOpts  = collect($profissionais)->values()->map(fn($p,$i) => ['value' => (string)$p['id'], 'label' => $p['nome'], 'color' => $palette[$i % count($palette)]])->toArray();
                        $convOpts  = collect($convenios)->values()->map(fn($c,$i)    => ['value' => (string)$c['id'], 'label' => $c['nome'], 'color' => $palette[$i % count($palette)]])->toArray();
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Tipo de Vínculo</label>
                            <x-select-dots wire="tipo_vinculo_id" :options="$tiposOpts" placeholder="Selecionar vínculo..." />
                        </div>
                        <div>
                            <label class="form-label">Profissional Responsável</label>
                            <x-select-dots wire="profissional_responsavel_id" :options="$profOpts" placeholder="Selecionar profissional..." />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Convênio / Forma de Pagamento</label>
                        <x-select-dots wire="convenio_id" :options="$convOpts" placeholder="Selecionar convênio..." />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Início do Plano</label>
                            <input wire:model="inicio_plano" type="date" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Fim do Plano</label>
                            <input wire:model="fim_plano" type="date" class="form-input" />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Informações Iniciais</label>
                        <textarea wire:model="informacoes_iniciais" rows="3" class="form-input resize-y" placeholder="Observações, histórico inicial..."></textarea>
                    </div>
                </div>

                {{-- TAB 4: Financeiro --}}
                <div x-show="tab === 4" class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ([
                            ['inadimplente',       'Inadimplente',          '#ef4444'],
                            ['tem_guia_finalizada','Tem Guia Finalizada',   '#22c55e'],
                            ['devendo_guia',       'Devendo Guia',          '#f59e0b'],
                            ['matricula_trancada', 'Matrícula Trancada',    '#94a3b8'],
                            ['controle_matricula', 'Controle de Matrícula', '#2563eb'],
                            ['financeiro_pendente','Financeiro Pendente',   '#f97316'],
                            ['atencao_informacoes','Atenção nas Informações','#8b5cf6'],
                        ] as [$prop, $label, $color])
                            <label class="flex items-start gap-2.5 p-3 border border-slate-100 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input wire:model="{{ $prop }}" type="checkbox" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-blue-600 flex-shrink-0" />
                                <div>
                                    <span class="text-xs font-semibold text-slate-700">{{ $label }}</span>
                                    <div class="w-1.5 h-1.5 rounded-full mt-1" style="background: {{ $color }}"></div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <button wire:click="fecharModal" type="button" class="btn btn-secondary">Cancelar</button>
                <button wire:click="salvar" type="button" class="btn btn-primary" wire:loading.attr="disabled">
                    <div wire:loading wire:target="salvar">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>
                    <x-lucide-save wire:loading.remove wire:target="salvar" class="w-4 h-4" />
                    {{ $pacienteId ? 'Atualizar' : 'Cadastrar' }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
