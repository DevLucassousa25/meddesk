@php
    $p       = $profissional;
    $temFoto = $p->foto && file_exists(storage_path('app/public/' . $p->foto));
    $inicial = mb_strtoupper(mb_substr($p->nome, 0, 1));

    $diasLabel = ['seg'=>'Seg','ter'=>'Ter','qua'=>'Qua','qui'=>'Qui','sex'=>'Sex','sab'=>'Sáb','dom'=>'Dom'];
@endphp

<div class="font-['Inter',system-ui,sans-serif] pb-14">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 mb-6 text-xs text-slate-400">
        <a href="{{ route('profissionais.lista') }}" class="hover:text-slate-600 transition-colors font-medium flex items-center gap-1.5">
            <x-lucide-users class="w-3.5 h-3.5" /> Profissionais
        </a>
        <span class="text-slate-200">/</span>
        <span class="text-slate-600 font-semibold">{{ $p->nome }}</span>
    </nav>

    {{-- ══ HERO ══ --}}
    <div class="rounded-2xl mb-5 overflow-hidden border border-slate-200/80">

        {{-- Banner --}}
        <div class="relative h-40 overflow-hidden" style="background: linear-gradient(135deg, #2563eb 0%, #2563ebdd 40%, #2563eb99 100%)">
            <svg class="absolute inset-0 w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg">
                <defs><pattern id="dp" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="1.5" fill="white"/>
                </pattern></defs>
                <rect width="100%" height="100%" fill="url(#dp)" />
            </svg>
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20 bg-white"></div>
            <div class="absolute -bottom-20 right-32 w-48 h-48 rounded-full opacity-10 bg-white"></div>

            <div class="absolute top-4 right-4 flex items-center gap-2">
                <a href="{{ route('profissionais.lista') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm transition-colors border border-white/20">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                </a>
                <a href="{{ route('profissionais.editar', $p->id) }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                </a>
            </div>
        </div>

        {{-- Corpo do hero --}}
        <div class="bg-white px-6 sm:px-8 pb-0">

            {{-- Avatar --}}
            <div class="relative z-10 -mt-[52px] mb-3 inline-block">
                <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg flex items-center justify-center text-3xl font-black bg-blue-100 text-blue-600">
                    @if($temFoto)
                        <img src="{{ asset('storage/' . $p->foto) }}" class="w-full h-full object-cover" alt="{{ $p->nome }}" />
                    @else
                        {{ $inicial }}
                    @endif
                </div>
                <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center {{ $p->ativo ? 'bg-emerald-500' : 'bg-slate-300' }}">
                    @if($p->ativo)<x-lucide-check class="w-3 h-3 text-white" />@endif
                </span>
            </div>

            {{-- Nome e badges --}}
            <div class="pb-5">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $p->nome }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $p->ativo ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $p->ativo ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $p->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                    @if($p->conselho_profissional)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            <x-lucide-shield-check class="w-3 h-3" />
                            {{ $p->conselho_profissional }}
                        </span>
                    @endif
                    @foreach($p->especialidades->take(3) as $esp)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100">
                            {{ $esp->nome }}
                        </span>
                    @endforeach
                    @if($p->especialidades->count() > 3)
                        <span class="text-xs text-slate-400">+{{ $p->especialidades->count() - 3 }} mais</span>
                    @endif
                    <span class="text-xs font-mono text-slate-400 font-semibold">{{ $p->identificacao }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5">
                    @if($p->email)
                        <span class="flex items-center gap-1.5 text-sm text-slate-500">
                            <x-lucide-mail class="w-3.5 h-3.5 text-slate-300" />{{ $p->email }}
                        </span>
                    @endif
                    @if($p->celular1)
                        <span class="flex items-center gap-1.5 text-sm text-slate-500">
                            <x-lucide-phone class="w-3.5 h-3.5 text-slate-300" />{{ $p->celular1 }}
                        </span>
                    @endif
                    @if($p->data_nascimento)
                        <span class="flex items-center gap-1.5 text-sm text-slate-500">
                            <x-lucide-cake class="w-3.5 h-3.5 text-slate-300" />
                            {{ $p->data_nascimento->format('d/m/Y') }} · <span class="font-semibold text-slate-600">{{ $p->data_nascimento->age }} anos</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 border-t border-slate-100 -mx-6 sm:-mx-8">
                @php
                    $statsHero = [
                        ['Pacientes',      $p->pacientes->count() . ' vinculados',    'lucide-users'],
                        ['Atendimentos',   $p->atendimentos()->count() . ' realizados','lucide-stethoscope'],
                        ['Especialidades', $p->especialidades->count() . ' cadastradas','lucide-award'],
                        ['Unidades',       $p->unidades->count() . ' vinculadas',     'lucide-building-2'],
                    ];
                @endphp
                @foreach($statsHero as $i => [$label, $value, $icon])
                    <div class="flex items-center gap-3 px-6 py-4 {{ $i < count($statsHero)-1 ? 'border-r border-slate-100' : '' }}">
                        <x-dynamic-component :component="$icon" class="w-4 h-4 text-slate-300 flex-shrink-0 hidden sm:block" />
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
                            <p class="text-sm font-semibold text-slate-700 truncate">{{ $value }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══ TABS ══ --}}
    @php
        $tabs = [
            ['geral',          'Visão Geral',    'lucide-layout-dashboard'],
            ['pacientes',      'Pacientes',       'lucide-users'],
            ['horarios',       'Horários',        'lucide-calendar-clock'],
            ['atendimentos',   'Atendimentos',    'lucide-stethoscope'],
            ['especialidades', 'Especialidades',  'lucide-award'],
        ];
    @endphp
    <div class="bg-white border border-slate-200 rounded-2xl mb-5 overflow-x-auto">
        <div class="flex items-center min-w-max">
            @foreach($tabs as [$key, $label, $icon])
                <button
                    wire:click="setTab('{{ $key }}')"
                    type="button"
                    class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                        {{ $tab === $key ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}"
                >
                    <x-dynamic-component :component="$icon" class="w-4 h-4" />
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ════ TAB: VISÃO GERAL ════ --}}
    @if($tab === 'geral')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-4">

            {{-- Dados Pessoais --}}
            @php
                $pessoais = array_filter([
                    ['CPF',           $p->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $p->cpf) : null],
                    ['RG',            $p->rg],
                    ['Nascimento',    $p->data_nascimento?->format('d/m/Y')],
                    ['Conselho',      $p->conselho_profissional],
                    ['Identificação', $p->identificacao],
                ], fn($f) => !empty($f[1]));
            @endphp
            @if(count($pessoais))
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                    <x-lucide-user class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Dados Pessoais</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach(array_chunk(array_values($pessoais), 3) as $row)
                        <div class="grid grid-cols-1 sm:grid-cols-3 divide-x divide-slate-50">
                            @foreach($row as $f)
                                <div class="px-6 py-4">
                                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">{{ $f[0] }}</p>
                                    <p class="text-sm font-semibold text-slate-800">{{ $f[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Endereço --}}
            @if($p->endereco || $p->cep)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                    <x-lucide-map-pin class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Endereço</span>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm font-semibold text-slate-800 mb-0.5">
                        {{ $p->endereco }}@if($p->numero), nº {{ $p->numero }}@endif
                    </p>
                    <p class="text-sm text-slate-500">@if($p->bairro){{ $p->bairro }},@endif {{ $p->cidade }}@if($p->uf) — {{ $p->uf }}@endif</p>
                    @if($p->cep)<p class="text-xs font-mono text-slate-400 mt-0.5">CEP {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', $p->cep) }}</p>@endif
                </div>
            </div>
            @endif

            {{-- Contato --}}
            @if($p->telefone || $p->celular1 || $p->email)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                    <x-lucide-phone class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Contato</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach([['Telefone',$p->telefone,'lucide-phone'],['Celular 1',$p->celular1,'lucide-smartphone'],['Celular 2',$p->celular2,'lucide-smartphone'],['E-mail',$p->email,'lucide-mail']] as [$lbl,$val,$icon])
                        @if($val)
                        <div class="flex items-center gap-4 px-6 py-3.5">
                            <x-dynamic-component :component="$icon" class="w-4 h-4 text-slate-300 flex-shrink-0" />
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ $lbl }}</p>
                                <p class="text-sm font-semibold text-slate-800">{{ $val }}</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Configurações de Agenda --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <x-lucide-calendar class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Agenda</span>
                </div>
                <div class="divide-y divide-slate-50">
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-xs text-slate-500">Ordem na agenda</span>
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $p->ordem_agenda }}º</span>
                    </div>
                    @foreach([
                        ['Pode estender horários', $p->pode_estender_horarios],
                        ['Horários flexíveis',     $p->horarios_flexiveis],
                        ['Receber lembrete evoluir',$p->receber_lembrete_evoluir],
                    ] as [$lbl, $val])
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-xs text-slate-500">{{ $lbl }}</span>
                        @if($val)
                            <span class="badge badge-green">Sim</span>
                        @else
                            <span class="badge badge-gray">Não</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Comissão --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <x-lucide-percent class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Comissão</span>
                </div>
                <div class="px-5 py-4">
                    @if($p->comissao_personalizada && $p->percentual_comissao)
                        @php $pct = rtrim(rtrim(number_format((float)$p->percentual_comissao, 2, '.', ''), '0'), '.'); @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex flex-col items-center justify-center flex-shrink-0">
                                <span class="text-xl font-black text-emerald-600 leading-none">{{ $pct }}</span>
                                <span class="text-[10px] font-bold text-emerald-400 leading-none mt-0.5">%</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Personalizada</p>
                                <p class="text-xs text-slate-400 mt-0.5">sobre atendimentos</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <x-lucide-percent class="w-4 h-4 text-slate-400" />
                            </div>
                            <p class="text-sm text-slate-500">Comissão padrão da clínica</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Permissões App --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <x-lucide-shield class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Permissões App</span>
                </div>
                <div class="p-4 space-y-1">
                    @foreach([
                        ['perm_ver_somente_seus',    'Ver somente seus'],
                        ['perm_fluxo_caixa',         'Fluxo de caixa'],
                        ['perm_agendar_celular',      'Agendar pelo celular'],
                        ['perm_editar_agenda',        'Editar agenda'],
                        ['perm_alterar_status',       'Alterar status'],
                        ['perm_acessar_cadastro',     'Acessar cadastro'],
                        ['perm_editar_recebimentos',  'Editar recebimentos'],
                        ['perm_remover_recebimentos', 'Remover recebimentos'],
                    ] as [$prop, $lbl])
                        @if($p->$prop)
                            <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-100">
                                <span class="text-xs font-semibold text-blue-700">{{ $lbl }}</span>
                                <x-lucide-check class="w-3.5 h-3.5 text-blue-500" />
                            </div>
                        @else
                            <div class="flex items-center px-3 py-1.5">
                                <span class="text-xs text-slate-400">{{ $lbl }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Registro --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <x-lucide-clock class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Registro</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach([['ID', '#'.$p->id, true],['Cadastrado em',$p->created_at->format('d/m/Y H:i'),false],['Atualizado em',$p->updated_at->format('d/m/Y H:i'),false]] as [$lbl,$val,$mono])
                    <div class="flex justify-between items-center px-5 py-3">
                        <span class="text-xs text-slate-400 font-medium">{{ $lbl }}</span>
                        <span class="text-xs font-bold text-slate-700 {{ $mono ? 'font-mono' : '' }}">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ════ TAB: PACIENTES ════ --}}
    @if($tab === 'pacientes')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <x-lucide-users class="w-4 h-4 text-slate-400" />
                <span class="text-sm font-semibold text-slate-700">Pacientes Vinculados</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">{{ $pacientes->total() }}</span>
            </div>
        </div>
        @if($pacientes->isEmpty())
            <div class="py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <x-lucide-users class="w-5 h-5 text-slate-300" />
                </div>
                <p class="text-sm font-medium text-slate-500">Nenhum paciente vinculado</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Paciente</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">CPF</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Convênio</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 w-16"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($pacientes as $pac)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('pacientes.detalhes', $pac->id) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition-colors">{{ $pac->nome }}</a>
                            <p class="text-[11px] text-slate-400">{{ $pac->codigo }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-slate-500 text-xs hidden sm:table-cell">{{ $pac->cpf ? \Str::mask($pac->cpf,'*',3,6) : '—' }}</td>
                        <td class="px-4 py-3.5 text-slate-600 text-xs hidden md:table-cell">{{ $pac->convenio?->nome ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            @if($pac->ativo)<span class="badge badge-green">Ativo</span>
                            @else<span class="badge badge-gray">Inativo</span>@endif
                        </td>
                        <td class="px-4 py-3.5">
                            <a href="{{ route('pacientes.detalhes', $pac->id) }}" class="opacity-0 group-hover:opacity-100 transition-opacity w-7 h-7 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600">
                                <x-lucide-eye class="w-3.5 h-3.5" />
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pacientes->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$pacientes" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: HORÁRIOS ════ --}}
    @if($tab === 'horarios')
    <div class="space-y-4">
        @forelse($p->unidades as $unidade)
        @php $dias = is_array($unidade->dias) ? $unidade->dias : json_decode($unidade->dias, true) ?? []; @endphp
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <x-lucide-building-2 class="w-4 h-4 text-blue-500" />
                </div>
                <span class="text-sm font-bold text-slate-800">{{ $unidade->unidade }}</span>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                    @foreach($diasLabel as $key => $label)
                    @php $dia = $dias[$key] ?? ['ativo'=>false,'inicio'=>'','fim'=>'']; @endphp
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl border transition-colors {{ ($dia['ativo'] ?? false) ? 'border-blue-200 bg-blue-50/50' : 'border-slate-100 bg-slate-50' }}">
                        <span class="w-8 text-xs font-bold {{ ($dia['ativo'] ?? false) ? 'text-blue-700' : 'text-slate-300' }} flex-shrink-0">{{ $label }}</span>
                        @if($dia['ativo'] ?? false)
                            <span class="text-xs text-slate-600 font-medium">
                                {{ substr($dia['inicio'] ?? '', 0, 5) }} – {{ substr($dia['fim'] ?? '', 0, 5) }}
                            </span>
                        @else
                            <span class="text-xs text-slate-300">Não atende</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white border border-slate-200 rounded-2xl py-14 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <x-lucide-calendar-clock class="w-5 h-5 text-slate-300" />
            </div>
            <p class="text-sm font-medium text-slate-500">Nenhuma unidade vinculada</p>
            <a href="{{ route('profissionais.editar', $p->id) }}" class="text-xs text-blue-500 hover:text-blue-700 mt-1 inline-block">Configurar horários →</a>
        </div>
        @endforelse
    </div>
    @endif

    {{-- ════ TAB: ATENDIMENTOS ════ --}}
    @if($tab === 'atendimentos')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
            <x-lucide-stethoscope class="w-4 h-4 text-slate-400" />
            <span class="text-sm font-semibold text-slate-700">Histórico de Atendimentos</span>
            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">{{ $atendimentos->total() }}</span>
        </div>
        @if($atendimentos->isEmpty())
            <div class="py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <x-lucide-stethoscope class="w-5 h-5 text-slate-300" />
                </div>
                <p class="text-sm font-medium text-slate-500">Nenhum atendimento realizado</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Data</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Paciente</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($atendimentos as $atd)
                    @php $sc=['realizado'=>['badge-green','Realizado'],'cancelado'=>['badge-red','Cancelado'],'faltou'=>['badge-gray','Faltou']][$atd->status]??['badge-gray',$atd->status]; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-800">{{ $atd->data_atendimento->format('d/m/Y') }}</p>
                            @if($atd->hora_atendimento)<p class="text-xs text-slate-400">{{ substr($atd->hora_atendimento,0,5) }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($atd->paciente)
                                <a href="{{ route('pacientes.detalhes', $atd->paciente->id) }}" class="font-medium text-slate-700 hover:text-blue-600 transition-colors">{{ $atd->paciente->nome }}</a>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 text-xs hidden sm:table-cell">{{ ucfirst($atd->tipo) }}</td>
                        <td class="px-4 py-3.5"><span class="badge {{ $sc[0] }}">{{ $sc[1] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($atendimentos->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$atendimentos" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: ESPECIALIDADES ════ --}}
    @if($tab === 'especialidades')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <x-lucide-award class="w-4 h-4 text-slate-400" />
                <span class="text-sm font-semibold text-slate-700">Especialidades</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">{{ $p->especialidades->count() }}</span>
            </div>
            <a href="{{ route('profissionais.editar', $p->id) }}" class="btn btn-primary text-xs gap-1.5">
                <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
            </a>
        </div>
        @if($p->especialidades->isEmpty())
            <div class="py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <x-lucide-award class="w-5 h-5 text-slate-300" />
                </div>
                <p class="text-sm font-medium text-slate-500">Nenhuma especialidade cadastrada</p>
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
            @foreach($p->especialidades as $esp)
            <div class="border border-slate-100 rounded-xl p-4 hover:border-violet-200 hover:bg-violet-50/30 transition-all">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                        <x-lucide-award class="w-4 h-4 text-violet-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $esp->nome }}</p>
                        @if($esp->empresa)<p class="text-xs text-slate-400 mt-0.5">{{ $esp->empresa }}</p>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

</div>
