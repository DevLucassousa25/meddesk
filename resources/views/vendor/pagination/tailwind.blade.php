@if ($paginator->hasPages())
<nav class="flex items-center gap-1" role="navigation">

    {{-- Primeira --}}
    @if ($paginator->onFirstPage())
        <span class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-300 border border-slate-200 cursor-not-allowed select-none">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7M19 19l-7-7 7-7"/></svg>
            First
        </span>
    @else
        <button wire:click="gotoPage(1)" class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7M19 19l-7-7 7-7"/></svg>
            First
        </button>
    @endif

    {{-- Anterior --}}
    @if ($paginator->onFirstPage())
        <span class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-300 border border-slate-200 cursor-not-allowed select-none">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </span>
    @else
        <button wire:click="previousPage" class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </button>
    @endif

    {{-- Páginas --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="w-8 h-8 flex items-center justify-center text-slate-400 text-xs select-none">⋯</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-900 text-white text-xs font-semibold border border-slate-900 select-none">
                        {{ $page }}
                    </span>
                @else
                    <button wire:click="gotoPage({{ $page }})"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:border-slate-400 hover:text-slate-800 transition-colors">
                        {{ $page }}
                    </button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Próxima --}}
    @if ($paginator->hasMorePages())
        <button wire:click="nextPage" class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:border-slate-400 hover:text-slate-800 transition-colors">
            Next
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    @else
        <span class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-300 border border-slate-200 cursor-not-allowed select-none">
            Next
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif

    {{-- Última --}}
    @if ($paginator->hasMorePages())
        <button wire:click="gotoPage({{ $paginator->lastPage() }})" class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:border-slate-400 hover:text-slate-800 transition-colors">
            Last
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5l7 7-7 7M13 5l7 7-7 7"/></svg>
        </button>
    @else
        <span class="h-8 px-3 flex items-center gap-1.5 rounded-lg text-xs font-medium text-slate-300 border border-slate-200 cursor-not-allowed select-none">
            Last
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5l7 7-7 7M13 5l7 7-7 7"/></svg>
        </span>
    @endif

</nav>
@endif
