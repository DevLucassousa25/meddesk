@props(['icon', 'texto', 'acao' => null, 'acaoLabel' => null, 'modalTipo' => null])
<div class="flex flex-col items-center justify-center py-16 gap-3 text-center">
    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-1">
        <x-dynamic-component :component="$icon" class="w-6 h-6 text-slate-300" />
    </div>
    <p class="text-sm font-semibold text-slate-500">{{ $texto }}</p>
    @if($modalTipo)
        <button wire:click="abrirModal('{{ $modalTipo }}')" type="button" class="btn btn-primary text-xs mt-1 gap-1.5">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ $acaoLabel ?? 'Adicionar' }}
        </button>
    @endif
</div>
