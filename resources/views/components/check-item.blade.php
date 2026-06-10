{{--
    Custom checkbox card component — syncs with Livewire via $wire.

    Usage (wire:model):
        <x-check-item wire="matricula_trancada" icon="lock" label="Matrícula Trancada" />

    Usage (alpine x-model, for parent x-data props):
        <x-check-item xmodel="cnpj" icon="id-card" label="Usar CNPJ" />
--}}
@props([
    'wire'   => '',
    'xmodel' => '',
    'label'  => '',
    'icon'   => '',
])

<label
    x-data="{
        chk: false,
        _wire: '{{ $wire }}',
        _xmodel: '{{ $xmodel }}',
        init() {
            if (this._wire) {
                this.chk = Boolean($wire[this._wire]);
                $wire.$watch(this._wire, v => { this.chk = Boolean(v); });
            }
        },
        toggle() {
            this.chk = !this.chk;
            if (this._wire) $wire[this._wire] = this.chk;
        }
    }"
    @if($xmodel)
        x-modelable="chk"
        x-model="{{ $xmodel }}"
    @endif
    @click.prevent="toggle()"
    class="group flex items-center gap-3 cursor-pointer select-none rounded-xl border px-3.5 py-3 transition-all duration-200"
    :class="chk
        ? 'bg-blue-50 border-blue-200 shadow-[0_1px_4px_rgba(37,99,235,.08)]'
        : 'bg-white border-slate-200 hover:border-blue-200 hover:bg-blue-50/40'"
    role="checkbox"
    :aria-checked="chk"
>
    {{-- Custom checkbox box --}}
    <div
        class="relative w-[18px] h-[18px] flex-shrink-0 rounded-[5px] border-2 transition-all duration-200 overflow-hidden"
        :class="chk
            ? 'bg-blue-600 border-blue-600 shadow-[0_0_0_3px_rgba(37,99,235,.15)]'
            : 'bg-white border-slate-300 group-hover:border-blue-400'"
    >
        {{-- Checkmark SVG — animates in/out --}}
        <svg
            class="absolute inset-0 w-full h-full text-white transition-all duration-200 ease-out"
            :class="chk ? 'opacity-100 scale-100' : 'opacity-0 scale-50'"
            viewBox="0 0 14 14"
            fill="none"
        >
            <path
                d="M2.5 7.5l3 3 6-6"
                stroke="currentColor"
                stroke-width="2.2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
    </div>

    {{-- Icon --}}
    @if($icon)
        <div
            class="flex-shrink-0 transition-colors duration-200"
            :class="chk ? 'text-blue-500' : 'text-slate-400 group-hover:text-blue-400'"
        >
            <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
        </div>
    @endif

    {{-- Label --}}
    <span
        class="text-[12.5px] font-semibold leading-none transition-colors duration-200 flex-1"
        :class="chk ? 'text-blue-700' : 'text-slate-600'"
    >{{ $label }}</span>

    {{-- Right indicator dot when checked --}}
    <div
        class="w-1.5 h-1.5 rounded-full flex-shrink-0 transition-all duration-200"
        :class="chk ? 'bg-blue-500 opacity-100' : 'opacity-0'"
    ></div>
</label>
