{{--
    <x-select-dots
        wire="status_cliente"
        placeholder="Selecionar..."
        :options="[
            ['value' => 'ativo',     'label' => 'Ativo',     'color' => '#22c55e'],
            ['value' => 'inativo',   'label' => 'Inativo',   'color' => '#94a3b8'],
        ]"
    />
--}}
@props([
    'wire'        => '',
    'options'     => [],
    'placeholder' => 'Selecionar...',
    'invalid'     => false,
])

<div
    x-data="{
        open: false,
        value: '',
        options: {{ Js::from($options) }},
        get selected() {
            return this.options.find(o => String(o.value) === String(this.value)) ?? null;
        },
        init() {
            const raw = $wire['{{ $wire }}'] ?? '';
            this.value = (raw === true || raw === 1) ? '1' : (raw === false || raw === 0) ? '0' : String(raw);
            $wire.$watch('{{ $wire }}', v => {
                this.value = (v === true || v === 1) ? '1' : (v === false || v === 0) ? '0' : String(v ?? '');
            });
        },
        choose(val) {
            this.value = String(val);
            // Convert '1'/'0' back to boolean when the Livewire property is boolean
            const parsed = val === '1' ? true : val === '0' ? false : val;
            $wire.set('{{ $wire }}', parsed);
            this.open = false;
        }
    }"
    @click.outside="open = false"
    class="relative"
>
    {{-- Trigger button --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full flex items-center gap-2.5 px-3 h-[38px] bg-[#f8fafc] border-[1.5px] rounded-[8px] text-left text-[13px] font-medium transition-all focus:outline-none cursor-pointer select-none {{ $invalid ? 'border-red-400 bg-[#fff8f8]' : 'border-[#e2e8f0]' }}"
        :class="open ? 'border-blue-500 bg-white shadow-[0_0_0_3px_rgba(37,99,235,.1)]' : 'hover:border-blue-300 hover:bg-white'"
    >
        {{-- Selected state --}}
        <template x-if="selected">
            <span class="flex items-center gap-2 flex-1 min-w-0">
                <span
                    class="w-2 h-2 rounded-full flex-shrink-0 ring-2 ring-white shadow-sm"
                    :style="'background-color:' + selected.color"
                ></span>
                <span class="truncate text-slate-700" x-text="selected.label"></span>
            </span>
        </template>

        {{-- Placeholder --}}
        <template x-if="!selected">
            <span class="flex-1 text-slate-400 text-[13px]">{{ $placeholder }}</span>
        </template>

        {{-- Chevron --}}
        <svg
            class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 transition-transform duration-150"
            :class="open ? 'rotate-180 text-blue-400' : ''"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100 origin-top"
        x-transition:enter-start="opacity-0 scale-y-[.96] -translate-y-0.5"
        x-transition:enter-end="opacity-100 scale-y-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75 origin-top"
        x-transition:leave-start="opacity-100 scale-y-100"
        x-transition:leave-end="opacity-0 scale-y-[.96]"
        class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200/80 rounded-xl shadow-[0_8px_24px_rgba(26,32,53,.12)] overflow-hidden"
        style="display:none"
    >
        <template x-for="opt in options" :key="opt.value">
            <button
                type="button"
                @click="choose(opt.value)"
                class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-[12.5px] text-left transition-colors cursor-pointer group"
                :class="String(value) === String(opt.value)
                    ? 'bg-blue-50 text-blue-700 font-semibold'
                    : 'text-slate-600 font-medium hover:bg-slate-50'"
            >
                {{-- Dot with a subtle glow on selected --}}
                <span
                    class="w-2 h-2 rounded-full flex-shrink-0 transition-transform group-hover:scale-110"
                    :class="String(value) === String(opt.value) ? 'scale-125 ring-2 ring-offset-1' : ''"
                    :style="'background-color:' + opt.color + ';' + (String(value) === String(opt.value) ? 'ring-color:' + opt.color + '40' : '')"
                ></span>

                {{-- Label --}}
                <span class="flex-1" x-text="opt.label"></span>

                {{-- Check mark when selected --}}
                <template x-if="String(value) === String(opt.value)">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
            </button>
        </template>
    </div>
</div>
