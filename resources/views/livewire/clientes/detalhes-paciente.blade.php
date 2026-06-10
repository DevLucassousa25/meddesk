@php
    $p = $paciente;

    $generoLabel = match($p->genero) {
        'masculino'     => 'Masculino',
        'feminino'      => 'Feminino',
        'outro'         => 'Outro',
        'nao_informado' => 'Não informado',
        default         => null,
    };

    $inicial  = mb_strtoupper(mb_substr($p->nome, 0, 1));
    $temFoto  = $p->foto && file_exists(storage_path('app/public/' . $p->foto));

    $accent = '#2563eb'; // hero sempre azul

    $flagsAtivos = collect([
        'inadimplente','tem_guia_finalizada','devendo_guia',
        'financeiro_pendente','matricula_trancada','controle_matricula','atencao_informacoes'
    ])->filter(fn($f) => $p->$f)->count();
@endphp

<div
    class="font-['Inter',system-ui,sans-serif] pb-14"
    x-data
    @modal-success.window="$store.modal.confirm({
        type: 'success',
        title: $event.detail.title,
        message: $event.detail.message,
        confirmText: 'OK',
        cancelText: null,
    })"
>

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 mb-6 text-xs text-slate-400">
        <a href="{{ route('pacientes.lista') }}" class="hover:text-slate-600 transition-colors font-medium flex items-center gap-1.5">
            <x-lucide-users class="w-3.5 h-3.5" /> Pacientes
        </a>
        <span class="text-slate-200">/</span>
        <span class="text-slate-600 font-semibold">{{ $p->nome }}</span>
    </nav>

    {{-- ══════════ HERO ══════════ --}}
    <div class="rounded-2xl mb-5 overflow-hidden shadow-sm border border-slate-200/80">

        {{-- Banner com gradiente --}}
        <div class="relative h-40 overflow-hidden" style="background: linear-gradient(135deg, {{ $accent }} 0%, {{ $accent }}dd 40%, {{ $accent }}99 100%);">
            {{-- Mesh pattern --}}
            <svg class="absolute inset-0 w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="hp" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="20" r="1.5" fill="white"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#hp)" />
            </svg>
            {{-- Blob decorativo --}}
            <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20" style="background:white"></div>
            <div class="absolute -bottom-20 right-32 w-48 h-48 rounded-full opacity-10" style="background:white"></div>

            {{-- Botões no topo direito --}}
            <div class="absolute top-4 right-4 flex items-center gap-2">
                <a href="{{ route('pacientes.lista') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm transition-colors border border-white/20">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                </a>
                <a href="{{ route('pacientes.editar', $p->id) }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                </a>
            </div>
        </div>

        {{-- Corpo do hero: avatar sobe sozinho, texto fica no branco --}}
        <div class="bg-white px-6 sm:px-8 pb-0">

            {{-- Avatar flutua para cima, isolado --}}
            <div class="relative z-10 -mt-[52px] mb-3 inline-block">
                <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg flex items-center justify-center text-3xl font-black"
                     style="{{ $temFoto ? 'background:#f8fafc' : 'background:#dbeafe;color:#2563eb' }}">
                    @if($temFoto)
                        <img src="{{ asset('storage/' . $p->foto) }}" class="w-full h-full object-cover" alt="{{ $p->nome }}" />
                    @else
                        {{ $inicial }}
                    @endif
                </div>
                <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full border-2 border-white flex items-center justify-center
                    {{ $p->ativo ? 'bg-emerald-500' : 'bg-slate-300' }}">
                    @if($p->ativo)<x-lucide-check class="w-3 h-3 text-white" />@endif
                </span>
            </div>

            {{-- Nome, badges e metadados — todos no branco --}}
            <div class="pb-5">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $p->nome }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                        {{ $p->ativo ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $p->ativo ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $p->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                    @if($p->inadimplente)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                            <x-lucide-alert-triangle class="w-3 h-3" /> Inadimplente
                        </span>
                    @endif
                    @if($p->atencao_informacoes)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100">
                            <x-lucide-alert-circle class="w-3 h-3" /> Atenção
                        </span>
                    @endif
                    <span class="text-xs font-mono text-slate-400 font-semibold">{{ $p->codigo }}</span>
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
                            {{ $p->data_nascimento->format('d/m/Y') }} &middot;
                            <span class="font-semibold text-slate-600">{{ $p->idade }} anos</span>
                        </span>
                    @endif
                    @if($p->convenio)
                        <span class="flex items-center gap-1.5 text-sm text-slate-500">
                            <x-lucide-shield class="w-3.5 h-3.5 text-slate-300" />{{ $p->convenio->nome }}
                        </span>
                    @endif
                    @if($p->profissionalResponsavel)
                        <span class="flex items-center gap-1.5 text-sm text-slate-500">
                            <x-lucide-user-check class="w-3.5 h-3.5 text-slate-300" />{{ $p->profissionalResponsavel->nome }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 border-t border-slate-100 -mx-6 sm:-mx-8">
                @php
                    $stats = [
                        ['Convênio',     $p->convenio?->nome ?? '—',                           'lucide-shield'],
                        ['Profissional', $p->profissionalResponsavel?->nome ?? '—',            'lucide-user-check'],
                        ['Cadastro',     $p->created_at->format('d/m/Y'),                      'lucide-calendar'],
                        ['Vínculo',      $p->tipoVinculo?->nome ?? '—',                        'lucide-link'],
                    ];
                @endphp
                @foreach($stats as $i => [$label, $value, $icon])
                    <div class="flex items-center gap-3 px-6 py-4 {{ $i < count($stats)-1 ? 'border-r border-slate-100' : '' }}">
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


    {{-- ══════════ TABS ══════════ --}}
    @php
        $tabs = [
            ['geral',        'Visão Geral',   'lucide-layout-dashboard'],
            ['atendimentos', 'Atendimentos',  'lucide-stethoscope'],
            ['agenda',       'Agenda',        'lucide-calendar'],
            ['prontuario',   'Prontuário',    'lucide-notebook-pen'],
            ['documentos',   'Documentos',    'lucide-file-text'],
            ['financeiro',   'Financeiro',    'lucide-wallet'],
            ['anotacoes',    'Anotações',     'lucide-message-square'],
            ['timeline',     'Linha do Tempo','lucide-activity'],
        ];
    @endphp

    {{-- Barra de tabs --}}
    <div class="bg-white border border-slate-200 rounded-2xl mb-5 overflow-x-auto">
        <div class="flex items-center min-w-max">
            @foreach($tabs as [$key, $label, $icon])
                <button
                    wire:click="setTab('{{ $key }}')"
                    type="button"
                    class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                        {{ $tab === $key
                            ? 'border-blue-600 text-blue-600 bg-blue-50/50'
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}"
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
                    ['CPF',        $p->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $p->cpf) : null],
                    ['CNPJ',       $p->cnpj],
                    ['RG',         $p->rg],
                    ['Gênero',     $generoLabel],
                    ['Nascimento', $p->data_nascimento?->format('d/m/Y')],
                    ['Idade',      $p->idade ? $p->idade . ' anos' : null],
                    ['Empresa',    $p->empresa],
                    ['Ativação',   $p->data_ativacao?->format('d/m/Y')],
                    ['Inativação', $p->data_inativacao?->format('d/m/Y')],
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
                    @if($p->endereco)
                        <p class="text-sm font-semibold text-slate-800 mb-0.5">
                            {{ $p->endereco }}@if($p->numero), nº {{ $p->numero }}@endif@if($p->complemento) — {{ $p->complemento }}@endif
                        </p>
                        <p class="text-sm text-slate-500">@if($p->bairro){{ $p->bairro }},@endif {{ $p->cidade }}@if($p->uf) — {{ $p->uf }}@endif</p>
                        @if($p->cep)<p class="text-xs font-mono text-slate-400 mt-0.5">CEP {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', $p->cep) }}</p>@endif
                    @endif
                </div>
            </div>
            @endif

            {{-- Contato --}}
            @if($p->telefone1 || $p->celular1 || $p->email)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                    <x-lucide-phone class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Contato</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach([['Telefone',$p->telefone1,'lucide-phone'],['Celular 1',$p->celular1,'lucide-smartphone'],['Celular 2',$p->celular2,'lucide-smartphone'],['E-mail',$p->email,'lucide-mail']] as [$lbl,$val,$icon])
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

            @if($p->informacoes_iniciais)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
                    <x-lucide-file-text class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Informações Iniciais</span>
                </div>
                <div class="px-6 py-5"><p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $p->informacoes_iniciais }}</p></div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <x-lucide-stethoscope class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Dados Clínicos</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach([['lucide-shield','Convênio',$p->convenio?->nome],['lucide-user-check','Profissional',$p->profissionalResponsavel?->nome],['lucide-link','Vínculo',$p->tipoVinculo?->nome]] as [$icon,$lbl,$val])
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $lbl }}</p>
                            <p class="text-sm font-semibold text-slate-700 truncate">{{ $val ?? '—' }}</p>
                        </div>
                    </div>
                    @endforeach
                    @if($p->inicio_plano || $p->fim_plano)
                    <div class="px-5 py-4">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Período do Plano</p>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="flex-1 text-center py-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase mb-0.5">Início</p>
                                <p class="font-bold text-slate-700 text-sm">{{ $p->inicio_plano?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                            <x-lucide-arrow-right class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
                            <div class="flex-1 text-center py-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase mb-0.5">Fim</p>
                                <p class="font-bold text-slate-700 text-sm">{{ $p->fim_plano?->format('d/m/Y') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-3"><x-lucide-wallet class="w-4 h-4 text-slate-400" /><span class="text-sm font-semibold text-slate-700">Situação Financeira</span></div>
                </div>
                <div class="p-4 space-y-1">
                    @foreach([['inadimplente','Inadimplente','text-red-600','bg-red-50 border-red-100'],['tem_guia_finalizada','Guia Finalizada','text-emerald-600','bg-emerald-50 border-emerald-100'],['devendo_guia','Devendo Guia','text-amber-600','bg-amber-50 border-amber-100'],['financeiro_pendente','Fin. Pendente','text-orange-600','bg-orange-50 border-orange-100'],['matricula_trancada','Mat. Trancada','text-slate-600','bg-slate-100 border-slate-200'],['controle_matricula','Ctrl. Matrícula','text-blue-600','bg-blue-50 border-blue-100'],['atencao_informacoes','Atenção','text-violet-600','bg-violet-50 border-violet-100']] as [$prop,$label,$tc,$bg])
                        @if($p->$prop)
                            <div class="flex items-center justify-between px-3 py-2 rounded-lg border {{ $bg }}">
                                <span class="text-xs font-semibold {{ $tc }}">{{ $label }}</span>
                                <x-lucide-check class="w-3.5 h-3.5 {{ $tc }}" />
                            </div>
                        @else
                            <div class="flex items-center px-3 py-2"><span class="text-xs text-slate-400">{{ $label }}</span></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                    <x-lucide-clock class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-semibold text-slate-700">Registro</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach([['Código',$p->codigo,true],['Cadastrado em',$p->created_at->format('d/m/Y H:i'),false],['Atualizado em',$p->updated_at->format('d/m/Y H:i'),false]] as [$lbl,$val,$mono])
                    <div class="flex justify-between items-center px-5 py-3">
                        <span class="text-xs text-slate-400 font-medium">{{ $lbl }}</span>
                        <span class="text-xs font-bold text-slate-700 {{ $mono ? 'font-mono tracking-wider' : '' }}">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ════ TAB: ATENDIMENTOS ════ --}}
    @if($tab === 'atendimentos')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3"><x-lucide-stethoscope class="w-4 h-4 text-slate-400" /><span class="text-sm font-semibold text-slate-700">Histórico de Atendimentos</span></div>
            <button wire:click="abrirModal('atendimento')" class="btn btn-primary text-xs gap-1.5"><x-lucide-plus class="w-3.5 h-3.5" /> Novo</button>
        </div>
        @if($atendimentos->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-stethoscope','texto'=>'Nenhum atendimento registrado','modalTipo'=>'atendimento','acaoLabel'=>'Registrar Atendimento'])
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Data</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Profissional</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 w-20"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($atendimentos as $atd)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-800">{{ $atd->data_atendimento->format('d/m/Y') }}</p>
                            @if($atd->hora_atendimento)<p class="text-xs text-slate-400">{{ substr($atd->hora_atendimento,0,5) }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 hidden sm:table-cell">{{ ucfirst($atd->tipo) }}</td>
                        <td class="px-4 py-3.5 text-slate-600 hidden md:table-cell">{{ $atd->profissional?->nome ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            @php $sc=['realizado'=>['badge-green','Realizado'],'cancelado'=>['badge-red','Cancelado'],'faltou'=>['badge-gray','Faltou']][$atd->status] ?? ['badge-gray',$atd->status]; @endphp
                            <span class="badge {{ $sc[0] }}">{{ $sc[1] }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="abrirModal('atendimento', {{ $atd->id }})" class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500"><x-lucide-pencil class="w-3.5 h-3.5" /></button>
                                <button wire:click="excluirAtendimento({{ $atd->id }})" wire:confirm="Excluir este atendimento?" class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400"><x-lucide-trash-2 class="w-3.5 h-3.5" /></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($atendimentos->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$atendimentos" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: AGENDA ════ --}}
    @if($tab === 'agenda')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3"><x-lucide-calendar class="w-4 h-4 text-slate-400" /><span class="text-sm font-semibold text-slate-700">Agendamentos</span></div>
            <button wire:click="abrirModal('agendamento')" class="btn btn-primary text-xs gap-1.5"><x-lucide-plus class="w-3.5 h-3.5" /> Novo</button>
        </div>
        @if($agendamentos->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-calendar','texto'=>'Nenhum agendamento','modalTipo'=>'agendamento','acaoLabel'=>'Novo Agendamento'])
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Data e Hora</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden md:table-cell">Profissional</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 w-20"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($agendamentos as $ag)
                    @php
                        $statusCores = ['agendado'=>['bg-blue-50 text-blue-700','Agendado'],'confirmado'=>['bg-teal-50 text-teal-700','Confirmado'],'realizado'=>['badge-green','Realizado'],'cancelado'=>['badge-red','Cancelado'],'faltou'=>['badge-gray','Faltou']];
                        $sc = $statusCores[$ag->status] ?? ['badge-gray',$ag->status];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-800">{{ $ag->data_hora->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $ag->data_hora->format('H:i') }} · {{ $ag->duracao_minutos }}min</p>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 hidden sm:table-cell">{{ ucfirst($ag->tipo) }}</td>
                        <td class="px-4 py-3.5 text-slate-600 hidden md:table-cell">{{ $ag->profissional?->nome ?? '—' }}</td>
                        <td class="px-4 py-3.5"><span class="badge {{ $sc[0] }}">{{ $sc[1] }}</span></td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="abrirModal('agendamento', {{ $ag->id }})" class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500"><x-lucide-pencil class="w-3.5 h-3.5" /></button>
                                <button wire:click="excluirAgendamento({{ $ag->id }})" wire:confirm="Excluir agendamento?" class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400"><x-lucide-trash-2 class="w-3.5 h-3.5" /></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($agendamentos->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$agendamentos" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: PRONTUÁRIO ════ --}}
    @if($tab === 'prontuario')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3"><x-lucide-notebook-pen class="w-4 h-4 text-slate-400" /><span class="text-sm font-semibold text-slate-700">Prontuário</span></div>
            <button wire:click="abrirModal('prontuario')" class="btn btn-primary text-xs gap-1.5"><x-lucide-plus class="w-3.5 h-3.5" /> Nova Entrada</button>
        </div>
        @if($prontuario->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-notebook-pen','texto'=>'Prontuário vazio','modalTipo'=>'prontuario','acaoLabel'=>'Adicionar Entrada'])
        @else
        <div class="divide-y divide-slate-100">
            @foreach($prontuario as $ent)
            @php
                $tipoCores=['anamnese'=>'bg-blue-50 text-blue-700','evolucao'=>'bg-emerald-50 text-emerald-700','prescricao'=>'bg-violet-50 text-violet-700','exame'=>'bg-amber-50 text-amber-700','cirurgia'=>'bg-red-50 text-red-700','outro'=>'bg-slate-100 text-slate-600'];
                $tc = $tipoCores[$ent->tipo] ?? 'bg-slate-100 text-slate-600';
            @endphp
            <div class="px-6 py-5 group hover:bg-slate-50/50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="flex flex-col items-center pt-0.5 flex-shrink-0">
                            <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5"></div>
                            <div class="w-px flex-1 bg-slate-100 mt-1"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-sm font-bold text-slate-800">{{ $ent->data_entrada->format('d/m/Y') }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $tc }}">{{ ucfirst($ent->tipo) }}</span>
                                @if($ent->profissional)<span class="text-xs text-slate-400">· {{ $ent->profissional->nome }}</span>@endif
                            </div>
                            @if($ent->titulo)<p class="text-sm font-semibold text-slate-700 mb-1">{{ $ent->titulo }}</p>@endif
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $ent->conteudo }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button wire:click="abrirModal('prontuario', {{ $ent->id }})" class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500"><x-lucide-pencil class="w-3.5 h-3.5" /></button>
                        <button wire:click="excluirProntuario({{ $ent->id }})" wire:confirm="Excluir esta entrada?" class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400"><x-lucide-trash-2 class="w-3.5 h-3.5" /></button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($prontuario->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$prontuario" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: DOCUMENTOS ════ --}}
    @if($tab === 'documentos')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3"><x-lucide-file-text class="w-4 h-4 text-slate-400" /><span class="text-sm font-semibold text-slate-700">Documentos ({{ $documentos->total() }})</span></div>
            <button wire:click="abrirModal('documento')" class="btn btn-primary text-xs gap-1.5"><x-lucide-upload class="w-3.5 h-3.5" /> Upload</button>
        </div>
        @if($documentos->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-file-text','texto'=>'Nenhum documento enviado','modalTipo'=>'documento','acaoLabel'=>'Enviar Documento'])
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
            @foreach($documentos as $doc)
            @php
                $icones=['exame'=>'lucide-microscope','laudo'=>'lucide-file-check','receita'=>'lucide-pill','guia'=>'lucide-clipboard-list','contrato'=>'lucide-file-signature','outros'=>'lucide-file'];
                $docIcon = $icones[$doc->tipo] ?? 'lucide-file';
                $docCores=['exame'=>'bg-blue-50 text-blue-500','laudo'=>'bg-teal-50 text-teal-500','receita'=>'bg-violet-50 text-violet-500','guia'=>'bg-amber-50 text-amber-500','contrato'=>'bg-slate-100 text-slate-500','outros'=>'bg-slate-100 text-slate-400'];
                $docCor = $docCores[$doc->tipo] ?? 'bg-slate-100 text-slate-400';
            @endphp
            <div class="border border-slate-100 rounded-xl p-4 hover:border-blue-200 hover:bg-blue-50/30 transition-all group">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $docCor }} flex items-center justify-center flex-shrink-0">
                        <x-dynamic-component :component="$docIcon" class="w-5 h-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $doc->nome }}</p>
                        <p class="text-xs text-slate-400">{{ ucfirst($doc->tipo) }} · {{ $doc->tamanho_formatado }}</p>
                        @if($doc->data_documento)<p class="text-xs text-slate-400">{{ $doc->data_documento->format('d/m/Y') }}</p>@endif
                    </div>
                </div>
                @if($doc->descricao)<p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $doc->descricao }}</p>@endif
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <a href="{{ Storage::url($doc->arquivo) }}" target="_blank" class="flex-1 text-center text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                        Ver arquivo
                    </a>
                    <button wire:click="excluirDocumento({{ $doc->id }})" wire:confirm="Excluir documento?" class="text-xs text-red-400 hover:text-red-600 transition-colors font-semibold">Excluir</button>
                </div>
            </div>
            @endforeach
        </div>
        @if($documentos->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$documentos" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: FINANCEIRO ════ --}}
    @if($tab === 'financeiro')
    {{-- Totais --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        @foreach([['Total','R$ '.number_format($finTotais['total'],2,',','.'),'lucide-wallet','bg-slate-100','text-slate-700'],['Pago','R$ '.number_format($finTotais['pago'],2,',','.'),'lucide-check-circle','bg-emerald-50','text-emerald-700'],['Pendente','R$ '.number_format($finTotais['pendente'],2,',','.'),'lucide-alert-circle','bg-red-50','text-red-600']] as [$lbl,$val,$icon,$bg,$tc])
        <div class="bg-white border border-slate-200 rounded-2xl p-4">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-8 h-8 rounded-xl {{ $bg }} flex items-center justify-center">
                    <x-dynamic-component :component="$icon" class="w-4 h-4 {{ $tc }}" />
                </div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ $lbl }}</span>
            </div>
            <p class="text-xl font-black {{ $tc }}">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3"><x-lucide-receipt class="w-4 h-4 text-slate-400" /><span class="text-sm font-semibold text-slate-700">Cobranças</span></div>
            <button wire:click="abrirModal('cobranca')" class="btn btn-primary text-xs gap-1.5"><x-lucide-plus class="w-3.5 h-3.5" /> Nova</button>
        </div>
        @if($cobrancas->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-receipt','texto'=>'Nenhuma cobrança registrada','modalTipo'=>'cobranca','acaoLabel'=>'Nova Cobrança'])
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Descrição</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Valor</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Vencimento</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 w-24"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($cobrancas as $cob)
                    @php
                        $cobStatus = $cob->status;
                        if($cobStatus !== 'pago' && $cobStatus !== 'cancelado' && $cob->data_vencimento->isPast()) $cobStatus = 'vencido';
                        $cobCores=['pago'=>'badge-green','pendente'=>'bg-amber-50 text-amber-700','vencido'=>'badge-red','cancelado'=>'badge-gray'];
                        $cobLabels=['pago'=>'Pago','pendente'=>'Pendente','vencido'=>'Vencido','cancelado'=>'Cancelado'];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-800">{{ $cob->descricao }}</p>
                            @if($cob->forma_pagamento)<p class="text-xs text-slate-400">{{ ucfirst(str_replace('_',' ',$cob->forma_pagamento)) }}</p>@endif
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-800">R$ {{ number_format($cob->valor,2,',','.') }}</td>
                        <td class="px-4 py-3.5 text-slate-600 hidden sm:table-cell">{{ $cob->data_vencimento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3.5"><span class="badge {{ $cobCores[$cobStatus] ?? 'badge-gray' }}">{{ $cobLabels[$cobStatus] ?? $cobStatus }}</span></td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($cobStatus !== 'pago' && $cobStatus !== 'cancelado')
                                    <button wire:click="marcarPago({{ $cob->id }})" class="w-7 h-7 rounded-lg hover:bg-emerald-50 flex items-center justify-center text-slate-400 hover:text-emerald-500" title="Marcar como pago"><x-lucide-check class="w-3.5 h-3.5" /></button>
                                @endif
                                <button wire:click="abrirModal('cobranca', {{ $cob->id }})" class="w-7 h-7 rounded-lg hover:bg-blue-50 flex items-center justify-center text-slate-400 hover:text-blue-500"><x-lucide-pencil class="w-3.5 h-3.5" /></button>
                                <button wire:click="excluirCobranca({{ $cob->id }})" wire:confirm="Excluir cobrança?" class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-center text-slate-400 hover:text-red-400"><x-lucide-trash-2 class="w-3.5 h-3.5" /></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($cobrancas->hasPages())<div class="px-5 py-3 border-t border-slate-100"><x-pagination :paginator="$cobrancas" /></div>@endif
        @endif
    </div>
    @endif

    {{-- ════ TAB: ANOTAÇÕES ════ --}}
    @if($tab === 'anotacoes')

    @php
        $corHex = [
            'blue'   => '#3b82f6',
            'green'  => '#10b981',
            'yellow' => '#f59e0b',
            'red'    => '#ef4444',
            'purple' => '#8b5cf6',
        ];
        $anotOrdenadas = $anotacoes->sortByDesc('fixada');
    @endphp

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <x-lucide-message-square class="w-4 h-4 text-slate-400" />
                <span class="text-sm font-semibold text-slate-700">Anotações</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">{{ $anotacoes->count() }}</span>
            </div>
            <button wire:click="abrirModal('anotacao')" class="btn btn-primary text-xs gap-1.5">
                <x-lucide-plus class="w-3.5 h-3.5" /> Nova Anotação
            </button>
        </div>

        @if($anotacoes->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-message-square','texto'=>'Nenhuma anotação','modalTipo'=>'anotacao','acaoLabel'=>'Adicionar Anotação'])
        @else

        <div class="divide-y divide-slate-50">
            @foreach($anotOrdenadas as $anot)
            @php $hex = $corHex[$anot->cor] ?? '#3b82f6'; @endphp

            <div class="group flex items-stretch hover:bg-slate-50/70 transition-colors">

                {{-- Barra lateral colorida --}}
                <div class="w-1 flex-shrink-0 rounded-r-full my-4" style="background: {{ $hex }}"></div>

                {{-- Conteúdo --}}
                <div class="flex-1 px-5 py-4 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">

                            {{-- Fixada badge --}}
                            @if($anot->fixada)
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <x-lucide-pin class="w-3 h-3 text-slate-400" />
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fixada</span>
                                </div>
                            @endif

                            {{-- Título --}}
                            @if($anot->titulo)
                                <p class="text-sm font-bold text-slate-800 mb-1 leading-snug">{{ $anot->titulo }}</p>
                            @endif

                            {{-- Conteúdo --}}
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap break-words">{{ $anot->conteudo }}</p>

                            {{-- Meta --}}
                            <div class="flex items-center gap-3 mt-3">
                                <span class="text-xs text-slate-400">{{ $anot->created_at->format('d/m/Y · H:i') }}</span>
                                @if($anot->updated_at->gt($anot->created_at->addMinutes(1)))
                                    <span class="text-xs text-slate-300">· editada {{ $anot->updated_at->diffForHumans() }}</span>
                                @endif
                                {{-- Cor --}}
                                <div class="w-2 h-2 rounded-full" style="background:{{ $hex }}" title="{{ ucfirst($anot->cor) }}"></div>
                            </div>
                        </div>

                        {{-- Ações (hover) --}}
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 pt-0.5">
                            <button
                                wire:click="toggleFixarAnotacao({{ $anot->id }})"
                                title="{{ $anot->fixada ? 'Desafixar' : 'Fixar' }}"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors text-slate-400 hover:bg-slate-200 hover:text-slate-600"
                            ><x-lucide-pin class="w-3.5 h-3.5 {{ $anot->fixada ? 'fill-current' : '' }}" /></button>
                            <button
                                wire:click="abrirModal('anotacao', {{ $anot->id }})"
                                title="Editar"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors text-slate-400 hover:bg-blue-50 hover:text-blue-500"
                            ><x-lucide-pencil class="w-3.5 h-3.5" /></button>
                            <button
                                wire:click="excluirAnotacao({{ $anot->id }})"
                                wire:confirm="Excluir esta anotação?"
                                title="Excluir"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors text-slate-400 hover:bg-red-50 hover:text-red-400"
                            ><x-lucide-trash-2 class="w-3.5 h-3.5" /></button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- ════ TAB: TIMELINE ════ --}}
    @if($tab === 'timeline')
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
            <x-lucide-activity class="w-4 h-4 text-slate-400" />
            <span class="text-sm font-semibold text-slate-700">Linha do Tempo</span>
        </div>
        @if($timeline->isEmpty())
            @include('livewire.clientes.partials.tab-empty', ['icon'=>'lucide-activity','texto'=>'Nenhum evento registrado'])
        @else
        <div class="p-6">
            <div class="relative">
                <div class="absolute left-[11px] top-0 bottom-0 w-px bg-slate-100"></div>
                <div class="space-y-5">
                    @foreach($timeline as $ev)
                    @php
                        $tlMap=[
                            'atendimento'=>['lucide-stethoscope','bg-blue-500','Atendimento'],
                            'agendamento'=>['lucide-calendar','bg-violet-500','Agenda'],
                            'cobranca'   =>['lucide-wallet','bg-amber-500','Financeiro'],
                            'prontuario' =>['lucide-notebook-pen','bg-teal-500','Prontuário'],
                        ];
                        [$tlIcon,$tlColor,$tlLabel] = $tlMap[$ev['tipo']] ?? ['lucide-circle','bg-slate-400','Evento'];
                        $tlStatusCores=['realizado'=>'badge-green','pago'=>'badge-green','confirmado'=>'bg-teal-50 text-teal-700','agendado'=>'bg-blue-50 text-blue-700','cancelado'=>'badge-red','vencido'=>'badge-red','faltou'=>'badge-gray','pendente'=>'bg-amber-50 text-amber-700','ok'=>'badge-green'];
                        $tlSC = $tlStatusCores[$ev['status']] ?? 'badge-gray';
                    @endphp
                    <div class="flex items-start gap-4 relative">
                        <div class="w-6 h-6 rounded-full {{ $tlColor }} flex items-center justify-center flex-shrink-0 z-10 shadow-sm">
                            <x-dynamic-component :component="$tlIcon" class="w-3 h-3 text-white" />
                        </div>
                        <div class="flex-1 min-w-0 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                            <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $tlLabel }}</span>
                                <span class="badge {{ $tlSC }} text-[10px]">{{ ucfirst($ev['status']) }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">{{ $ev['texto'] }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($ev['data'])->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ════ MODAIS ════ --}}
    @if($modal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
        <div class="fixed inset-0 bg-slate-900/50" wire:click="fecharModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Header do modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
                <h3 class="text-base font-bold text-slate-800">
                    @if($modalTipo === 'atendimento') {{ $editandoId ? 'Editar' : 'Novo' }} Atendimento
                    @elseif($modalTipo === 'agendamento') {{ $editandoId ? 'Editar' : 'Novo' }} Agendamento
                    @elseif($modalTipo === 'documento') Upload de Documento
                    @elseif($modalTipo === 'anotacao') {{ $editandoId ? 'Editar' : 'Nova' }} Anotação
                    @elseif($modalTipo === 'cobranca') {{ $editandoId ? 'Editar' : 'Nova' }} Cobrança
                    @elseif($modalTipo === 'prontuario') {{ $editandoId ? 'Editar Entrada' : 'Nova Entrada' }} — Prontuário
                    @endif
                </h3>
                <button wire:click="fecharModal" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400"><x-lucide-x class="w-4 h-4" /></button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-4">
                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 space-y-0.5">
                        @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
                    </div>
                @endif

                {{-- ─ Modal: Atendimento ─ --}}
                @if($modalTipo === 'atendimento')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Data <span class="text-red-400">*</span></label>
                        <x-datepicker wire="atd_data" placeholder="dd/mm/aaaa" />
                    </div>
                    <div>
                        <label class="form-label">Hora</label>
                        <input wire:model="atd_hora" type="time" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Tipo</label>
                        <x-select wire="atd_tipo" :options="collect(\App\Models\Atendimento::tipos())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <x-select wire="atd_status" :options="collect(\App\Models\Atendimento::statusList())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Profissional</label>
                        <x-select wire="atd_profissional" :options="collect($profissionais)->map(fn($p)=>['value'=>(string)$p['id'],'label'=>$p['nome']])->toArray()" placeholder="Selecionar profissional..." icon="lucide-user-check" />
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Local</label>
                        <input wire:model="atd_local" type="text" placeholder="Sala, consultório..." class="form-input" />
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Observações</label>
                        <textarea wire:model="atd_observacoes" rows="3" class="form-input resize-y"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="salvarAtendimento" wire:loading.attr="disabled" class="btn btn-primary">
                        <svg wire:loading wire:target="salvarAtendimento" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <span wire:loading wire:target="salvarAtendimento">Salvando...</span>
                        <span wire:loading.remove wire:target="salvarAtendimento">Salvar</span>
                    </button>
                </div>
                @endif

                {{-- ─ Modal: Agendamento ─ --}}
                @if($modalTipo === 'agendamento')
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="form-label">Data e Hora <span class="text-red-400">*</span></label>
                        <input wire:model="ag_data_hora" type="datetime-local" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Tipo</label>
                        <x-select wire="ag_tipo" :options="collect(\App\Models\Atendimento::tipos())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div>
                        <label class="form-label">Duração (min)</label>
                        <x-select wire="ag_duracao" :options="collect([15,20,30,45,60,90,120])->map(fn($d)=>['value'=>(string)$d,'label'=>$d.' min'])->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <x-select wire="ag_status" :options="collect(\App\Models\Agendamento::statusList())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div>
                        <label class="form-label">Profissional</label>
                        <x-select wire="ag_profissional" :options="collect($profissionais)->map(fn($p)=>['value'=>(string)$p['id'],'label'=>$p['nome']])->toArray()" placeholder="Selecionar profissional..." icon="lucide-user-check" />
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Observações</label>
                        <textarea wire:model="ag_observacoes" rows="2" class="form-input resize-y"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="salvarAgendamento" wire:loading.attr="disabled" class="btn btn-primary">
                        <svg wire:loading wire:target="salvarAgendamento" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <span wire:loading wire:target="salvarAgendamento">Salvando...</span>
                        <span wire:loading.remove wire:target="salvarAgendamento">Salvar</span>
                    </button>
                </div>
                @endif

                {{-- ─ Modal: Documento ─ --}}
                @if($modalTipo === 'documento')
                <div class="space-y-3">
                    <div>
                        <label class="form-label">Arquivo {{ !$editandoId ? '*' : '' }}</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-blue-300 transition-colors">
                            <input type="file" wire:model="doc_arquivo" class="hidden" id="doc-file-input" />
                            <label for="doc-file-input" class="cursor-pointer">
                                @if($doc_arquivo)
                                    <div class="flex items-center justify-center gap-2 text-sm text-emerald-600 font-medium">
                                        <x-lucide-check-circle class="w-5 h-5" />
                                        {{ $doc_arquivo->getClientOriginalName() }}
                                    </div>
                                @else
                                    <x-lucide-upload class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                                    <p class="text-sm text-slate-500 font-medium">Clique para selecionar</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Máx. 10 MB</p>
                                @endif
                            </label>
                        </div>
                        <div wire:loading wire:target="doc_arquivo" class="text-xs text-blue-600 mt-1">Enviando...</div>
                    </div>
                    <div>
                        <label class="form-label">Nome <span class="text-red-400">*</span></label>
                        <input wire:model="doc_nome" type="text" class="form-input" placeholder="Nome do documento" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Tipo</label>
                            <x-select wire="doc_tipo" :options="collect(\App\Models\DocumentoPaciente::tipos())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                        </div>
                        <div>
                            <label class="form-label">Data do Documento</label>
                            <x-datepicker wire="doc_data" placeholder="dd/mm/aaaa" />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Descrição</label>
                        <textarea wire:model="doc_descricao" rows="2" class="form-input resize-y" placeholder="Observações sobre o documento..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="salvarDocumento" class="btn btn-primary" wire:loading.attr="disabled" wire:target="salvarDocumento,doc_arquivo">
                        <span wire:loading wire:target="salvarDocumento"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg></span>
                        Salvar
                    </button>
                </div>
                @endif

                {{-- ─ Modal: Anotação ─ --}}
                @if($modalTipo === 'anotacao')
                <div class="space-y-3">
                    <div>
                        <label class="form-label">Título (opcional)</label>
                        <input wire:model="anot_titulo" type="text" class="form-input" placeholder="Título da anotação" />
                    </div>
                    <div>
                        <label class="form-label">Conteúdo <span class="text-red-400">*</span></label>
                        <textarea wire:model="anot_conteudo" rows="4" class="form-input resize-y" placeholder="Escreva a anotação..."></textarea>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="form-label">Cor</label>
                            <div class="flex gap-2 mt-1">
                                @foreach(['blue'=>'#3b82f6','green'=>'#10b981','yellow'=>'#f59e0b','red'=>'#ef4444','purple'=>'#8b5cf6'] as $cor=>$hex)
                                    <button type="button" wire:click="$set('anot_cor','{{ $cor }}')"
                                        class="w-7 h-7 rounded-full border-2 transition-all {{ $anot_cor === $cor ? 'border-slate-800 scale-110' : 'border-transparent' }}"
                                        style="background:{{ $hex }}"></button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Fixar</label>
                            <div class="flex items-center gap-2 h-9">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input wire:model="anot_fixada" type="checkbox" class="sr-only peer" />
                                    <div class="w-9 h-5 bg-slate-200 peer-checked:bg-blue-600 rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-4"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="salvarAnotacao" wire:loading.attr="disabled" class="btn btn-primary">
                        <svg wire:loading wire:target="salvarAnotacao" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <span wire:loading wire:target="salvarAnotacao">Salvando...</span>
                        <span wire:loading.remove wire:target="salvarAnotacao">Salvar</span>
                    </button>
                </div>
                @endif

                {{-- ─ Modal: Cobrança ─ --}}
                @if($modalTipo === 'cobranca')
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="form-label">Descrição <span class="text-red-400">*</span></label>
                        <input wire:model="cob_descricao" type="text" class="form-input" placeholder="Ex: Consulta, Sessão, Exame..." />
                    </div>
                    <div>
                        <label class="form-label">Valor (R$) <span class="text-red-400">*</span></label>
                        <input wire:model="cob_valor" type="text" class="form-input" placeholder="0,00" />
                    </div>
                    <div>
                        <label class="form-label">Vencimento <span class="text-red-400">*</span></label>
                        <x-datepicker wire="cob_vencimento" placeholder="dd/mm/aaaa" />
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <x-select wire="cob_status" :options="collect(\App\Models\Cobranca::statusList())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div>
                        <label class="form-label">Forma de Pagamento</label>
                        <x-select wire="cob_forma" :options="collect(\App\Models\Cobranca::formasPagamento())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Data de Pagamento</label>
                        <x-datepicker wire="cob_pagamento" placeholder="dd/mm/aaaa" />
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Observações</label>
                        <textarea wire:model="cob_observacoes" rows="2" class="form-input resize-y"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="salvarCobranca" wire:loading.attr="disabled" class="btn btn-primary">
                        <svg wire:loading wire:target="salvarCobranca" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <span wire:loading wire:target="salvarCobranca">Salvando...</span>
                        <span wire:loading.remove wire:target="salvarCobranca">Salvar</span>
                    </button>
                </div>
                @endif

                {{-- ─ Modal: Prontuário ─ --}}
                @if($modalTipo === 'prontuario')
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Data <span class="text-red-400">*</span></label>
                            <x-datepicker wire="pron_data" placeholder="dd/mm/aaaa" />
                        </div>
                        <div>
                            <label class="form-label">Tipo</label>
                            <x-select wire="pron_tipo" :options="collect(\App\Models\ProntuarioEntrada::tipos())->map(fn($v,$k)=>['value'=>$k,'label'=>$v])->values()->toArray()" placeholder="Selecionar..." />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Profissional</label>
                        <x-select wire="pron_profissional" :options="collect($profissionais)->map(fn($p)=>['value'=>(string)$p['id'],'label'=>$p['nome']])->toArray()" placeholder="Selecionar profissional..." icon="lucide-user-check" />
                    </div>
                    <div>
                        <label class="form-label">Título (opcional)</label>
                        <input wire:model="pron_titulo" type="text" class="form-input" placeholder="Título da entrada" />
                    </div>
                    <div>
                        <label class="form-label">Conteúdo <span class="text-red-400">*</span></label>
                        <textarea wire:model="pron_conteudo" rows="6" class="form-input resize-y" placeholder="Descrição, observações, anamnese..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="fecharModal" class="btn btn-secondary">Cancelar</button>
                    <button wire:click="salvarProntuario" wire:loading.attr="disabled" class="btn btn-primary">
                        <svg wire:loading wire:target="salvarProntuario" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <span wire:loading wire:target="salvarProntuario">Salvando...</span>
                        <span wire:loading.remove wire:target="salvarProntuario">Salvar</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>
