<div class="{{ ($span ?? false) ? 'col-span-2 sm:col-span-3' : '' }}">
    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-0.5">{{ $label }}</p>
    @if($value ?? null)
        <p class="text-sm font-medium text-slate-700">{{ $value }}</p>
    @else
        <p class="text-sm text-slate-300">—</p>
    @endif
</div>
