{{--
    ┌────────────────────────────────────────────────────────────┐
    │  <x-alert />  — Alert inline reutilizável                  │
    │                                                            │
    │  Props:                                                    │
    │    type      → success | info | warning | error            │
    │    title     → texto do título (opcional)                  │
    │    message   → texto da mensagem (ou use slot padrão)      │
    │    dismissible → true | false (padrão false)               │
    │    icon      → true | false (padrão true)                  │
    │    compact   → true | false (padrão false)                 │
    │                                                            │
    │  Exemplos:                                                 │
    │    <x-alert type="success" message="Salvo com sucesso!" /> │
    │    <x-alert type="error" title="Erro" dismissible>         │
    │        Não foi possível concluir a operação.               │
    │    </x-alert>                                              │
    └────────────────────────────────────────────────────────────┘
--}}

@props([
    'type'        => 'info',
    'title'       => null,
    'message'     => null,
    'dismissible' => false,
    'icon'        => true,
    'compact'     => false,
])

@php
    $paleta = [
        'success' => [
            'wrap'    => 'bg-emerald-50 border-emerald-200 text-emerald-800',
            'strip'   => 'bg-emerald-500',
            'iconBg'  => 'bg-emerald-100',
            'iconClr' => 'text-emerald-600',
            'title'   => 'text-emerald-900',
            'msg'     => 'text-emerald-700',
            'close'   => 'text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100',
            'label'   => 'Sucesso',
        ],
        'info' => [
            'wrap'    => 'bg-blue-50 border-blue-200 text-blue-800',
            'strip'   => 'bg-blue-500',
            'iconBg'  => 'bg-blue-100',
            'iconClr' => 'text-blue-600',
            'title'   => 'text-blue-900',
            'msg'     => 'text-blue-700',
            'close'   => 'text-blue-400 hover:text-blue-600 hover:bg-blue-100',
            'label'   => 'Informação',
        ],
        'warning' => [
            'wrap'    => 'bg-amber-50 border-amber-200 text-amber-800',
            'strip'   => 'bg-amber-400',
            'iconBg'  => 'bg-amber-100',
            'iconClr' => 'text-amber-600',
            'title'   => 'text-amber-900',
            'msg'     => 'text-amber-700',
            'close'   => 'text-amber-400 hover:text-amber-600 hover:bg-amber-100',
            'label'   => 'Atenção',
        ],
        'error' => [
            'wrap'    => 'bg-red-50 border-red-200 text-red-800',
            'strip'   => 'bg-red-500',
            'iconBg'  => 'bg-red-100',
            'iconClr' => 'text-red-600',
            'title'   => 'text-red-900',
            'msg'     => 'text-red-700',
            'close'   => 'text-red-400 hover:text-red-600 hover:bg-red-100',
            'label'   => 'Erro',
        ],
    ];

    $c = $paleta[$type] ?? $paleta['info'];

    $iconPath = match($type) {
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>',
        'error'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>',
        default   => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>',
    };

    $displayTitle = $title ?? $c['label'];
    $pad = $compact ? 'p-3' : 'p-4';
@endphp

<div
    {{ $attributes->merge(['class' => 'rounded-2xl border overflow-hidden ' . $c['wrap']]) }}
    @if($dismissible) x-data="{ show: true }" x-show="show" @endif
>
    {{-- Barra superior colorida --}}
    <div class="h-[3px] {{ $c['strip'] }}"></div>

    <div class="{{ $pad }} flex items-start gap-3">

        {{-- Ícone --}}
        @if($icon)
        <div class="flex-shrink-0 w-8 h-8 rounded-xl {{ $c['iconBg'] }} {{ $c['iconClr'] }} flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2.5" width="16" height="16">
                {!! $iconPath !!}
            </svg>
        </div>
        @endif

        {{-- Conteúdo --}}
        <div class="flex-1 min-w-0">
            @if(!$compact)
                <p class="text-sm font-bold {{ $c['title'] }} leading-snug mb-0.5">{{ $displayTitle }}</p>
            @endif

            @if($message)
                <p class="text-sm {{ $c['msg'] }} leading-relaxed {{ $compact ? 'font-medium' : '' }}">{{ $message }}</p>
            @elseif($slot->isNotEmpty())
                <div class="text-sm {{ $c['msg'] }} leading-relaxed">{{ $slot }}</div>
            @endif
        </div>

        {{-- Botão fechar --}}
        @if($dismissible)
        <button
            @click="show = false"
            class="flex-shrink-0 w-6 h-6 rounded-lg flex items-center justify-center transition-colors {{ $c['close'] }}"
        >
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
        @endif

    </div>
</div>
