<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MedDesk' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; background: #f4f6fb; margin: 0; }
        [x-cloak] { display: none !important; }

        /* Cursor pointer em qualquer elemento clicável */
        button, [role="button"], label[for], a[href],
        [wire\:click], [x-on\:click], [\@click],
        select, summary { cursor: pointer; }
        button:disabled, [disabled] { cursor: not-allowed; opacity: .6; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px; min-height: 100vh; background: #fff;
            border-right: 1px solid #eef0f5; display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; z-index: 50;
            transform: translateX(-100%);
            transition: transform .25s cubic-bezier(.4,0,.2,1);
        }
        .sidebar.open { transform: translateX(0); }
        @media (min-width: 1024px) {
            .sidebar { transform: translateX(0); }
        }
        .sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 0 20px; height: 60px; border-bottom: 1px solid #eef0f5; flex-shrink: 0; }
        .sidebar-logo-icon { width: 34px; height: 34px; background: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sidebar-logo-name { font-size: 15px; font-weight: 700; color: #1e293b; }
        .sidebar-clinic { padding: 12px 16px 8px; border-bottom: 1px solid #eef0f5; }
        .sidebar-clinic-name { font-size: 13px; font-weight: 600; color: #1e293b; }
        .sidebar-clinic-addr { font-size: 11px; color: #94a3b8; }
        .filial-item { width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;background:none;border:none;cursor:pointer;text-align:left;transition:background .12s; }
        .filial-item:hover { background:#f8fafc; }
        .filial-item-active { background:#f0f7ff; }
        .filial-icon { width:26px;height:26px;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .sidebar-group { padding: 16px 12px 4px; }
        .sidebar-group-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; color: #94a3b8; text-transform: uppercase; padding: 0 8px 6px; }
        .sidebar-item { display: flex; align-items: center; gap: 9px; padding: 7px 10px; border-radius: 8px; font-size: 13px; color: #475569; cursor: pointer; text-decoration: none; transition: all .15s; margin-bottom: 1px; }
        .sidebar-item:hover { background: #f1f5f9; color: #1e293b; }
        .sidebar-item.active { background: #eff6ff; color: #2563eb; font-weight: 600; }
        .sidebar-item svg { width: 16px; height: 16px; flex-shrink: 0; }
        .sidebar-bottom { margin-top: auto; border-top: 1px solid #eef0f5; padding: 12px; }

        /* ── Topbar ── */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: 60px;
            background: #fff; border-bottom: 1px solid #eef0f5;
            display: flex; align-items: center; padding: 0 16px;
            z-index: 40; gap: 10px;
        }
        @media (min-width: 1024px) {
            .topbar { left: 220px; padding: 0 28px; gap: 16px; }
        }
        .topbar-hamburger { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all .15s; border: none; background: transparent; flex-shrink: 0; }
        .topbar-hamburger:hover { background: #f1f5f9; color: #1e293b; }
        @media (min-width: 1024px) { .topbar-hamburger { display: none !important; } }

        /* Title fills remaining space and truncates — no separate spacer needed */
        .topbar-title { font-size: 17px; font-weight: 700; color: #1e293b; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        @media (min-width: 640px) { .topbar-title { font-size: 20px; } }

        .topbar-search { display: none; max-width: 280px; align-items: center; gap: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 7px 14px; flex-shrink: 0; }
        @media (min-width: 768px) { .topbar-search { display: flex; } }
        .topbar-search input { background: transparent; border: none; outline: none; font-size: 13px; color: #64748b; width: 100%; }
        .topbar-search svg { width: 15px; height: 15px; color: #94a3b8; flex-shrink: 0; }

        .topbar-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }

        /* Use CSS-based hide/show — avoids Tailwind specificity conflict */
        .topbar-icon-btn { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all .15s; border: none; background: transparent; }
        .topbar-icon-btn:hover { background: #f1f5f9; color: #1e293b; }
        .topbar-icon-btn svg { width: 18px; height: 18px; }
        .topbar-icon-btn-sm { display: none; }
        @media (min-width: 640px) { .topbar-icon-btn-sm { display: flex; } }
        .topbar-icon-btn-md { display: none; }
        @media (min-width: 768px) { .topbar-icon-btn-md { display: flex; } }

        .topbar-add { width: 36px; height: 36px; background: #2563eb; border-radius: 9px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; color: #fff; flex-shrink: 0; }
        .topbar-add svg { width: 18px; height: 18px; }
        .topbar-user { display: flex; align-items: center; gap: 8px; padding: 4px 8px; border-radius: 10px; cursor: pointer; flex-shrink: 0; }
        .topbar-user:hover { background: #f8fafc; }
        .topbar-avatar { width: 32px; height: 32px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .topbar-user-info { display: none; line-height: 1.2; }
        @media (min-width: 768px) { .topbar-user-info { display: block; } }
        .topbar-chevron { display: none; }
        @media (min-width: 768px) { .topbar-chevron { display: block; } }
        .topbar-user-name { font-size: 13px; font-weight: 600; color: #1e293b; }
        .topbar-user-role { font-size: 11px; color: #94a3b8; }

        /* ── Main ── */
        .main-content { margin-left: 0; margin-top: 60px; padding: 16px; min-height: calc(100vh - 60px); }
        @media (min-width: 640px) { .main-content { padding: 20px; } }
        @media (min-width: 1024px) { .main-content { margin-left: 220px; padding: 28px; } }

        /* ── Form / Card globals ── */
        .form-label { display: block; font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 5px; }
        .form-input { width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 8px 11px; font-size: 13px; color: #1e293b; outline: none; transition: border-color .15s, box-shadow .15s; font-family: inherit; }
        .form-input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
        .form-input.is-invalid { border-color: #ef4444 !important; background: #fff8f8; }
        .form-input.is-invalid:focus { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,.10); }
        .form-input[readonly] { color: #64748b; cursor: default; }
        select.form-input { appearance: auto; cursor: pointer; }
        textarea.form-input { resize: vertical; }
        .card { background: #fff; border-radius: 14px; border: 1px solid #eef0f5; }
        .card-header { display: flex; align-items: center; gap: 8px; padding: 14px 18px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
        .card-header-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card-header-icon svg { width: 14px; height: 14px; }
        .card-title { font-size: 13px; font-weight: 700; color: #1e293b; }
        .card-body { padding: 16px 18px; }
        @media (min-width: 640px) { .card-header { padding: 16px 20px; } .card-body { padding: 20px; } }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .15s; white-space: nowrap; font-family: inherit; }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; color: #1e293b; }
        .btn-success { background: #059669; color: #fff; }
        .btn-success:hover { background: #047857; }
        .btn-teal { background: #0d9488; color: #fff; }
        .btn-teal:hover { background: #0f766e; }
        .badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-gray { background: #f1f5f9; color: #64748b; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .dropdown-item { display: flex; align-items: center; gap: 9px; width: 100%; padding: 7px 14px; font-size: 12.5px; font-weight: 500; color: #374151; background: transparent; border: none; cursor: pointer; text-align: left; font-family: inherit; transition: background .1s; }
        .dropdown-item:hover { background: #f8fafc; }

        /* ── Flatpickr overrides ── */
        .flatpickr-calendar {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(26,32,53,.10);
            overflow: hidden;
        }
        .flatpickr-months { background: #2563eb; padding: 6px 0; }
        .flatpickr-months .flatpickr-month,
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month { color: #fff; fill: #fff; }
        .flatpickr-months .flatpickr-prev-month:hover svg,
        .flatpickr-months .flatpickr-next-month:hover svg { fill: #bfdbfe; }
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year { color: #fff; font-weight: 600; font-size: 14px; font-family: inherit; }
        .flatpickr-current-month .flatpickr-monthDropdown-months:hover,
        .flatpickr-current-month input.cur-year:hover { background: rgba(255,255,255,.15); }
        .flatpickr-weekdays { background: #eff6ff; }
        span.flatpickr-weekday { color: #2563eb; font-weight: 600; font-size: 11px; }
        .flatpickr-day { border-radius: 8px; font-size: 13px; color: #374151; }
        .flatpickr-day:hover { background: #eff6ff; border-color: #eff6ff; }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: #2563eb; border-color: #2563eb; color: #fff; font-weight: 600;
        }
        .flatpickr-day.today { border-color: #2563eb; color: #2563eb; font-weight: 600; }
        .flatpickr-day.today:hover { background: #eff6ff; color: #2563eb; }
        .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover { color: #cbd5e1; }
        .flatpickr-input.form-input { cursor: pointer; }
        .flatpickr-input[readonly] { cursor: pointer; color: #1e293b; background: #f8fafc; }
        /* alt input (the visible display input) */
        .flatpickr-input.form-input ~ .flatpickr-input { display: none; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">

    {{-- Mobile backdrop --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden"
    ></div>

    {{-- Sidebar --}}
    <aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.2" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </div>
            <span class="sidebar-logo-name">MedDesk</span>
        </div>

        @php
            $todasUnidades = \App\Models\Unidade::ativas()->orderBy('nome')->get();
            $filialAtivaId = session('unidade_ativa_id');
            $filialAtiva   = $filialAtivaId ? $todasUnidades->firstWhere('id', $filialAtivaId) : null;
            $temFiliais    = $todasUnidades->count() > 1;
            $nomeExibido   = $filialAtiva?->nome ?? ($todasUnidades->first()?->nome ?? 'Sinapse Care');
            $endExibido    = $filialAtiva
                ? ($filialAtiva->cidade ? $filialAtiva->cidade.($filialAtiva->uf ? ' — '.$filialAtiva->uf : '') : 'Filial selecionada')
                : ($temFiliais ? null : ($todasUnidades->first()?->cidade ? $todasUnidades->first()->cidade.($todasUnidades->first()->uf ? ' — '.$todasUnidades->first()->uf : '') : 'Espaço Multidisciplinar'));
        @endphp

        @if($temFiliais)
        <div class="sidebar-clinic" x-data="{ open: false }" style="position:relative;">
            <button type="button" x-on:click="open=!open"
                style="width:100%;display:flex;align-items:center;gap:8px;padding:0;background:none;border:none;cursor:pointer;text-align:left;">
                <div style="width:30px;height:30px;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;overflow:hidden;">
                    <div class="sidebar-clinic-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $nomeExibido }}</div>
                    <div class="sidebar-clinic-addr" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        @if($filialAtiva) {{ $endExibido }}
                        @else <span style="color:#3b82f6;font-weight:600;">Todas as filiais</span>
                        @endif
                    </div>
                </div>
                <svg x-bind:style="open ? 'transform:rotate(180deg)' : ''" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2.5" style="flex-shrink:0;transition:transform .2s;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-on:click.outside="open=false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display:none;position:absolute;left:0;right:0;top:calc(100% + 4px);background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.10);z-index:9999;overflow:hidden;">

                {{-- Todas --}}
                <form method="POST" action="{{ route('filial.trocar') }}">
                    @csrf <input type="hidden" name="unidade_id" value="todas">
                    <button type="submit" class="filial-item {{ !$filialAtivaId ? 'filial-item-active' : '' }}">
                        <div class="filial-icon" style="background:#eff6ff;">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        </div>
                        <div style="flex:1;min-width:0;"><div style="font-size:12px;font-weight:600;color:#1e293b;">Todas as filiais</div><div style="font-size:10px;color:#94a3b8;">Dados consolidados</div></div>
                        @if(!$filialAtivaId)<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="3" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                </form>
                <div style="height:1px;background:#f1f5f9;margin:2px 8px;"></div>

                @foreach($todasUnidades as $u)
                <form method="POST" action="{{ route('filial.trocar') }}">
                    @csrf <input type="hidden" name="unidade_id" value="{{ $u->id }}">
                    <button type="submit" class="filial-item {{ $filialAtivaId===$u->id ? 'filial-item-active' : '' }}">
                        <div class="filial-icon" style="background:{{ ($u->tipo??'filial')==='principal' ? '#eff6ff' : '#f1f5f9' }};">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="{{ ($u->tipo??'filial')==='principal' ? '#3b82f6' : '#64748b' }}" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="font-size:12px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u->nome }}</span>
                                @if(($u->tipo??'filial')==='principal')<span style="font-size:9px;font-weight:700;color:#3b82f6;background:#eff6ff;padding:1px 5px;border-radius:4px;flex-shrink:0;">SEDE</span>@endif
                            </div>
                            @if($u->cidade)<div style="font-size:10px;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $u->cidade }}{{ $u->uf ? ' — '.$u->uf : '' }}</div>@endif
                        </div>
                        @if($filialAtivaId===$u->id)<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="3" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                </form>
                @endforeach
            </div>
        </div>
        @else
        <div class="sidebar-clinic">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:30px;height:30px;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;overflow:hidden;">
                    <div class="sidebar-clinic-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $nomeExibido }}</div>
                    <div class="sidebar-clinic-addr" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $endExibido ?? 'Espaço Multidisciplinar' }}</div>
                </div>
            </div>
        </div>
        @endif

        <div style="overflow-y:auto;flex:1;">
            <div class="sidebar-group">
                <div class="sidebar-group-label">Clínica</div>
                <a href="#" class="sidebar-item"><x-lucide-calendar-days /> Agenda</a>
                <a href="{{ route('pacientes.lista') }}" class="sidebar-item {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                    <x-lucide-users /> Pacientes
                </a>
                <a href="#" class="sidebar-item"><x-lucide-stethoscope /> Atendimentos</a>
                <a href="{{ route('profissionais.lista') }}" class="sidebar-item {{ request()->routeIs('profissionais.*') ? 'active' : '' }}">
                    <x-lucide-user-check /> Equipe
                </a>
                <a href="{{ route('config.salas') }}" class="sidebar-item {{ request()->routeIs('config.salas') ? 'active' : '' }}">
                    <x-lucide-door-open /> Salas
                </a>
            </div>
            <div class="sidebar-group">
                <div class="sidebar-group-label">Financeiro</div>
                <a href="#" class="sidebar-item"><x-lucide-landmark /> Contas</a>
                <a href="#" class="sidebar-item"><x-lucide-trending-up /> Vendas</a>
                <a href="#" class="sidebar-item"><x-lucide-shopping-cart /> Compras</a>
                <a href="#" class="sidebar-item"><x-lucide-credit-card /> Formas de Pgto.</a>
                <a href="{{ route('config.tabela-precos') }}" class="sidebar-item {{ request()->routeIs('config.tabela-precos') ? 'active' : '' }}">
                    <x-lucide-tag /> Tabela de Preços
                </a>
                <a href="{{ route('financeiro.pacotes') }}" class="sidebar-item {{ request()->routeIs('financeiro.pacotes') ? 'active' : '' }}">
                    <x-lucide-package /> Pacotes
                </a>
            </div>
            <div class="sidebar-group">
                <div class="sidebar-group-label">Relatórios</div>
                <a href="#" class="sidebar-item"><x-lucide-bar-chart-2 /> Relatórios</a>
                <a href="#" class="sidebar-item"><x-lucide-headphones /> Suporte</a>
            </div>
            <div class="sidebar-group">
                <div class="sidebar-group-label">Configurações</div>
                <a href="{{ route('config.especialidades') }}" class="sidebar-item {{ request()->routeIs('config.especialidades') ? 'active' : '' }}">
                    <x-lucide-stethoscope /> Especialidades
                </a>
                <a href="{{ route('config.unidades') }}" class="sidebar-item {{ request()->routeIs('config.unidades') ? 'active' : '' }}">
                    <x-lucide-building-2 /> Unidades
                </a>
            </div>
        </div>

        <div class="sidebar-bottom">
            <a href="#" class="sidebar-item" style="color:#ef4444;">
                <x-lucide-log-out /> Sair
            </a>
        </div>
    </aside>

    {{-- Topbar --}}
    <header class="topbar">
        {{-- Hamburger (mobile only) --}}
        <button class="topbar-hamburger" @click="sidebarOpen = !sidebarOpen" aria-label="Menu">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="topbar-title">{{ $title ?? 'MedDesk' }}</div>

        <div class="topbar-search">
            <x-lucide-search style="width:15px;height:15px;color:#94a3b8;" />
            <input type="text" placeholder="Buscar..." />
        </div>

        <div class="topbar-actions">
            <button class="topbar-add"><x-lucide-plus style="width:18px;height:18px;" /></button>
            <button class="topbar-icon-btn"><x-lucide-bell style="width:18px;height:18px;" /></button>
            <button class="topbar-icon-btn topbar-icon-btn-sm"><x-lucide-settings style="width:18px;height:18px;" /></button>
        </div>

        <div class="topbar-user">
            <div class="topbar-avatar">A</div>
            <div class="topbar-user-info">
                <div class="topbar-user-name">Admin</div>
                <div class="topbar-user-role">Super admin</div>
            </div>
            <span class="topbar-chevron">
                <x-lucide-chevron-down style="width:14px;height:14px;color:#94a3b8;" />
            </span>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="main-content">

        {{-- Banner de filial ativa --}}
        @php $bannerFilial = $filialAtiva ?? null; @endphp
        @if($bannerFilial)
        <div class="flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium mb-5 shadow-sm shadow-blue-200">
            <div class="flex items-center gap-2.5">
                <x-lucide-building-2 class="w-4 h-4 opacity-80 flex-shrink-0" />
                <span>Exibindo dados da filial: <strong>{{ $bannerFilial->nome }}</strong></span>
                @if($bannerFilial->cidade)
                    <span class="opacity-60 text-xs hidden sm:inline">— {{ $bannerFilial->cidade }}@if($bannerFilial->uf) / {{ $bannerFilial->uf }}@endif</span>
                @endif
            </div>
            <form method="POST" action="{{ route('filial.trocar') }}" class="flex-shrink-0">
                @csrf
                <input type="hidden" name="unidade_id" value="todas" />
                <button type="submit"
                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-semibold transition-colors">
                    <x-lucide-x class="w-3 h-3" /> Ver todas
                </button>
            </form>
        </div>
        @endif

        {{ $slot }}
    </main>

    {{-- Sistema global de notificações toast --}}
    <x-toast-container />

    {{-- Modal de confirmação global --}}
    <x-confirm-modal />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/pt.min.js"></script>
    @livewireScripts
</body>
</html>
