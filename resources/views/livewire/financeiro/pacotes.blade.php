<div class="font-['Inter',system-ui,sans-serif]">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Pacotes</h1>
            <p class="text-sm text-slate-400 mt-0.5">Crie pacotes de consultas e exames com desconto</p>
        </div>
        <button type="button" wire:click="abrirCadastro"
            class="btn btn-primary gap-2 h-10 px-5 text-sm rounded-xl shadow-sm shadow-blue-200">
            <x-lucide-plus class="w-4 h-4" /> Novo Pacote
        </button>
    </div>

    {{-- Busca --}}
    <div class="bg-white border border-slate-100 rounded-xl mb-4">
        <div class="flex items-center gap-2.5 px-4 h-11">
            <x-lucide-search class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
            <input wire:model.live.debounce.300ms="busca" type="text"
                placeholder="Buscar pacote..."
                class="bg-transparent border-none outline-none text-sm text-slate-700 placeholder-slate-400 w-full" />
            @if($busca)
                <button wire:click="$set('busca','')" class="text-slate-300 hover:text-slate-500">
                    <x-lucide-x class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
    </div>

    {{-- Lista de pacotes --}}
    @if($pacotes->isEmpty())
    <div class="bg-white border border-slate-100 rounded-xl px-5 py-16 text-center">
        <div class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-1">
                <x-lucide-package class="w-6 h-6 text-slate-300" />
            </div>
            <p class="text-sm font-semibold text-slate-500">Nenhum pacote cadastrado</p>
            <p class="text-xs text-slate-400">Crie pacotes com múltiplos itens e desconto especial.</p>
            <button type="button" wire:click="abrirCadastro" class="btn btn-primary text-xs mt-2 gap-1.5">
                <x-lucide-plus class="w-3.5 h-3.5" /> Novo Pacote
            </button>
        </div>
    </div>
    @else
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($pacotes as $pacote)
        @php
            $economia = (float)$pacote->valor_bruto - (float)$pacote->valor_final;
            $percEco  = $pacote->valor_bruto > 0 ? round($economia / $pacote->valor_bruto * 100, 1) : 0;
        @endphp
        <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-md transition-shadow group">

            {{-- Top colorido --}}
            <div class="h-1.5 bg-gradient-to-r from-blue-500 to-violet-500"></div>

            <div class="p-5">
                {{-- Nome + status --}}
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-800 text-base leading-tight truncate">{{ $pacote->nome }}</h3>
                        @if($pacote->descricao)
                            <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ $pacote->descricao }}</p>
                        @endif
                    </div>
                    <button type="button" wire:click="toggleAtivo({{ $pacote->id }})">
                        @if($pacote->ativo)
                            <span class="badge badge-green flex-shrink-0">Ativo</span>
                        @else
                            <span class="badge badge-gray flex-shrink-0">Inativo</span>
                        @endif
                    </button>
                </div>

                {{-- Valores --}}
                <div class="bg-slate-50 rounded-xl p-3 mb-3">
                    <div class="flex items-end justify-between gap-2">
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Valor do Pacote</p>
                            <p class="text-2xl font-bold text-slate-800">
                                R$ {{ number_format((float)$pacote->valor_final, 2, ',', '.') }}
                            </p>
                            @if($economia > 0)
                            <p class="text-[11px] text-slate-400 line-through mt-0.5">
                                R$ {{ number_format((float)$pacote->valor_bruto, 2, ',', '.') }}
                            </p>
                            @endif
                        </div>
                        @if($economia > 0)
                        <div class="text-right flex-shrink-0">
                            <div class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-lg">
                                {{ $percEco }}% off
                            </div>
                            <p class="text-[11px] text-emerald-600 font-medium mt-1">
                                Economia de R$ {{ number_format($economia, 2, ',', '.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Itens e validade --}}
                <div class="flex items-center gap-3 text-xs text-slate-500 mb-4">
                    <div class="flex items-center gap-1.5">
                        <x-lucide-list class="w-3.5 h-3.5 text-slate-400" />
                        {{ $pacote->itens_count }} {{ $pacote->itens_count === 1 ? 'item' : 'itens' }}
                    </div>
                    @if($pacote->validade_dias)
                    <div class="flex items-center gap-1.5">
                        <x-lucide-clock class="w-3.5 h-3.5 text-slate-400" />
                        {{ $pacote->validade_dias }} dias de validade
                    </div>
                    @endif
                    @if($pacote->unidade)
                    <div class="flex items-center gap-1.5">
                        <x-lucide-building-2 class="w-3.5 h-3.5 text-slate-400" />
                        {{ $pacote->unidade->nome }}
                    </div>
                    @endif
                </div>

                {{-- Ações --}}
                <div class="flex gap-2">
                    <button type="button" wire:click="editar({{ $pacote->id }})"
                        class="flex-1 h-9 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-colors flex items-center justify-center gap-1.5">
                        <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                    </button>
                    <button type="button"
                        wire:click="excluir({{ $pacote->id }})"
                        wire:confirm="Deseja excluir o pacote '{{ $pacote->nome }}'?"
                        class="h-9 w-9 rounded-xl border border-slate-200 text-slate-400 hover:border-red-200 hover:bg-red-50 hover:text-red-500 transition-colors flex items-center justify-center">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══ Modal Cadastro / Edição ══ --}}
    @if($modalAberto)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px]" wire:click="fecharModal"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <x-lucide-package class="w-4 h-4 text-blue-500" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">
                        {{ $editandoId ? 'Editar Pacote' : 'Novo Pacote' }}
                    </h3>
                </div>
                <button wire:click="fecharModal" type="button"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body (scroll) --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                {{-- Nome + Descrição --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">Nome do Pacote <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nome"
                            placeholder="Ex: Pacote Inicial, Check-up Completo..."
                            class="form-input @error('nome') is-invalid @enderror" />
                        @error('nome') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Descrição</label>
                        <textarea wire:model="descricao" rows="2"
                            placeholder="Descreva o que está incluso no pacote..."
                            class="form-input resize-none"></textarea>
                    </div>
                </div>

                {{-- Itens do pacote --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">Itens do Pacote <span class="text-red-500">*</span></label>
                        @if(!empty($itens))
                        <span class="text-[11px] text-slate-400">{{ count($itens) }} item(ns)</span>
                        @endif
                    </div>

                    {{-- Seletor de item --}}
                    <div x-data="{ search: '', open: false, items: {{ Js::from($tabelaPrecos->groupBy('tipo')->map(fn($g) => $g->values())->toArray()) }} }"
                        class="relative mb-3">

                        <div @click="open = !open"
                            class="flex items-center gap-2 px-3 h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-white transition-all select-none"
                            :class="open ? 'border-blue-400 bg-white shadow-[0_0_0_3px_rgba(37,99,235,.08)]' : ''">
                            <x-lucide-plus class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" />
                            <span class="text-sm text-slate-500 flex-1">Adicionar consulta, exame ou atendimento...</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        <div x-show="open" @click.outside="open = false; search = ''"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            style="display:none"
                            class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 overflow-hidden">

                            {{-- Search --}}
                            <div class="px-3 pt-2.5 pb-1.5 border-b border-slate-100">
                                <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-2.5 h-8 border border-slate-200 focus-within:border-blue-400 focus-within:bg-white transition-colors">
                                    <x-lucide-search class="w-3 h-3 text-slate-400 flex-shrink-0" />
                                    <input x-model="search" type="text" placeholder="Buscar..."
                                        x-effect="if(open) $nextTick(() => $el.focus())"
                                        @click.stop
                                        class="flex-1 bg-transparent border-none outline-none text-xs text-slate-700 placeholder-slate-400" />
                                </div>
                            </div>

                            <div class="max-h-52 overflow-y-auto py-1.5">
                                @php
                                    $grupos = [
                                        'consulta'    => ['label' => 'Consultas',    'cor' => 'text-blue-500',    'bg' => 'bg-blue-50'],
                                        'exame'       => ['label' => 'Exames',       'cor' => 'text-violet-500',  'bg' => 'bg-violet-50'],
                                        'atendimento' => ['label' => 'Atendimentos', 'cor' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                                    ];
                                @endphp
                                @foreach($grupos as $tipo => $cfg)
                                @php $itensTipo = $tabelaPrecos->where('tipo', $tipo); @endphp
                                @if($itensTipo->isNotEmpty())
                                <div x-show="!search || {{ Js::from($itensTipo->pluck('nome')->join(' ')) }}.toLowerCase().includes(search.toLowerCase())">
                                    <p class="px-3.5 pt-2 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $cfg['label'] }}</p>
                                    @foreach($itensTipo as $preco)
                                    <button type="button"
                                        x-show="!search || '{{ strtolower($preco->nome) }}'.includes(search.toLowerCase())"
                                        wire:click="adicionarItem({{ $preco->id }})"
                                        @click="open = false; search = ''"
                                        class="w-full flex items-center gap-3 px-3.5 py-2 hover:bg-slate-50 transition-colors text-left">
                                        <div class="w-6 h-6 rounded-md {{ $cfg['bg'] }} flex items-center justify-center flex-shrink-0">
                                            @if($tipo === 'consulta')
                                                <x-lucide-stethoscope class="w-3 h-3 {{ $cfg['cor'] }}" />
                                            @elseif($tipo === 'exame')
                                                <x-lucide-flask-conical class="w-3 h-3 {{ $cfg['cor'] }}" />
                                            @else
                                                <x-lucide-heart-pulse class="w-3 h-3 {{ $cfg['cor'] }}" />
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $preco->nome }}</p>
                                            @if($preco->codigo)
                                                <p class="text-[10px] text-slate-400">{{ $preco->codigo }}</p>
                                            @endif
                                        </div>
                                        <span class="text-xs font-semibold text-slate-600 flex-shrink-0">
                                            R$ {{ number_format((float)$preco->valor, 2, ',', '.') }}
                                        </span>
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                                @endforeach

                                @if($tabelaPrecos->isEmpty())
                                <p class="px-4 py-4 text-xs text-slate-400 text-center">
                                    Nenhum item na tabela de preços.
                                    <a href="{{ route('config.tabela-precos') }}" class="text-blue-500 hover:underline">Cadastrar agora</a>
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Itens adicionados --}}
                    @if(!empty($itens))
                    <div class="border border-slate-100 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="text-left px-3 py-2 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Item</th>
                                    <th class="text-center px-2 py-2 text-[10px] font-semibold text-slate-400 uppercase tracking-wider w-24">Qtd.</th>
                                    <th class="text-right px-3 py-2 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Subtotal</th>
                                    <th class="w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($itens as $idx => $item)
                                @php
                                    $cfgItem = [
                                        'consulta'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-500'],
                                        'exame'       => ['bg' => 'bg-violet-50',  'text' => 'text-violet-500'],
                                        'atendimento' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500'],
                                    ][$item['tipo']] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-400'];
                                @endphp
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-3 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-md {{ $cfgItem['bg'] }} flex items-center justify-center flex-shrink-0">
                                                @if($item['tipo'] === 'consulta')
                                                    <x-lucide-stethoscope class="w-3 h-3 {{ $cfgItem['text'] }}" />
                                                @elseif($item['tipo'] === 'exame')
                                                    <x-lucide-flask-conical class="w-3 h-3 {{ $cfgItem['text'] }}" />
                                                @else
                                                    <x-lucide-heart-pulse class="w-3 h-3 {{ $cfgItem['text'] }}" />
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-700 text-xs leading-tight">{{ $item['nome'] }}</p>
                                                <p class="text-[10px] text-slate-400">R$ {{ number_format($item['valor_unitario'], 2, ',', '.') }} /un</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" wire:click="decrementarQtd({{ $idx }})"
                                                class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                                <x-lucide-minus class="w-3 h-3" />
                                            </button>
                                            <span class="w-7 text-center text-sm font-semibold text-slate-700">{{ $item['quantidade'] }}</span>
                                            <button type="button" wire:click="incrementarQtd({{ $idx }})"
                                                class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                                <x-lucide-plus class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span class="font-semibold text-slate-700 text-xs">
                                            R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="pr-2 py-2.5">
                                        <button type="button" wire:click="removerItem({{ $idx }})"
                                            class="w-6 h-6 rounded-md hover:bg-red-50 flex items-center justify-center text-slate-300 hover:text-red-400 transition-colors">
                                            <x-lucide-x class="w-3 h-3" />
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Desconto --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Tipo de Desconto</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('tipo_desconto','percentual')"
                                class="h-10 rounded-xl border-2 text-xs font-semibold transition-all flex items-center justify-center gap-1.5
                                    {{ $tipo_desconto === 'percentual' ? 'border-blue-400 bg-blue-50 text-blue-600' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                                <x-lucide-percent class="w-3.5 h-3.5" /> Percentual
                            </button>
                            <button type="button" wire:click="$set('tipo_desconto','fixo')"
                                class="h-10 rounded-xl border-2 text-xs font-semibold transition-all flex items-center justify-center gap-1.5
                                    {{ $tipo_desconto === 'fixo' ? 'border-blue-400 bg-blue-50 text-blue-600' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                                <x-lucide-tag class="w-3.5 h-3.5" /> Valor fixo
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">
                            {{ $tipo_desconto === 'percentual' ? 'Desconto (%)' : 'Desconto (R$)' }}
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-semibold">
                                {{ $tipo_desconto === 'percentual' ? '%' : 'R$' }}
                            </span>
                            <input type="text" wire:model.live="desconto"
                                placeholder="0"
                                style="padding-left: 2rem"
                                class="form-input @error('desconto') is-invalid @enderror" />
                        </div>
                    </div>
                </div>

                {{-- Resumo de valores --}}
                @if(!empty($itens))
                <div class="bg-slate-50 rounded-xl p-4 space-y-2">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal ({{ count($itens) }} itens)</span>
                        <span>R$ {{ number_format($valorBruto, 2, ',', '.') }}</span>
                    </div>
                    @if($economiaVal > 0)
                    <div class="flex justify-between text-sm text-emerald-600">
                        <span>Desconto</span>
                        <span>− R$ {{ number_format($economiaVal, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-slate-800 pt-2 border-t border-slate-200">
                        <span>Total do Pacote</span>
                        <span class="text-lg">R$ {{ number_format($valorFinal, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                {{-- Validade + Unidade --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Validade (dias)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-lucide-calendar class="w-3.5 h-3.5" />
                            </span>
                            <input type="number" wire:model="validade_dias" min="1" max="3650"
                                placeholder="Ex: 90"
                                style="padding-left: 2.25rem"
                                class="form-input @error('validade_dias') is-invalid @enderror" />
                        </div>
                        @error('validade_dias') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Unidade / Filial</label>
                        <x-select
                            wire="unidade_id"
                            :options="$unidades->map(fn($u) => ['value' => $u->id, 'label' => $u->nome])->toArray()"
                            placeholder="— Todas —"
                            icon="lucide-building-2"
                        />
                    </div>
                </div>

                {{-- Ativo --}}
                <x-check-item wire="ativo" icon="circle-check" label="Pacote ativo e disponível para venda" />
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl flex-shrink-0">
                <button type="button" wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                <button type="button" wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
                    <svg wire:loading wire:target="salvar" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <x-lucide-check wire:loading.remove wire:target="salvar" class="w-3.5 h-3.5" />
                    <span wire:loading wire:target="salvar">Salvando...</span>
                    <span wire:loading.remove wire:target="salvar">{{ $editandoId ? 'Salvar Alterações' : 'Criar Pacote' }}</span>
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
