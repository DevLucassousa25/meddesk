{{--
    <x-select>  —  Select customizado com dropdown em position:fixed
    (escapa de overflow:hidden em modais e containers scrolláveis)

    Props:
      wire        → nome da propriedade Livewire
      options     → array de ['value'=>'...','label'=>'...','sub'=>'...']
      placeholder → texto quando nada selecionado
      searchable  → habilita busca interna
      invalid     → estado de erro
      icon        → lucide icon prefix (ex: 'lucide-building-2')
--}}

@props([
    'wire'        => '',
    'options'     => [],
    'placeholder' => 'Selecionar...',
    'searchable'  => false,
    'invalid'     => false,
    'icon'        => null,
])

<div
    x-data="{
        open: false,
        search: '',
        value: '',
        options: {{ Js::from($options) }},
        dropStyle: {},

        get selected() {
            return this.options.find(o => String(o.value) === String(this.value)) ?? null;
        },
        get filtered() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },

        init() {
            const raw = $wire['{{ $wire }}'] ?? '';
            this.value = String(raw ?? '');
            $wire.$watch('{{ $wire }}', v => {
                this.value = String(v ?? '');
            });
        },

        toggle() {
            if (!this.open) {
                // Calcular posição do trigger para o dropdown fixed
                const rect = this.$refs.trigger.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                const dropHeight = Math.min(220, this.options.length * 36 + 12);
                const above = spaceBelow < dropHeight && rect.top > dropHeight;

                this.dropStyle = {
                    position: 'fixed',
                    left:  rect.left + 'px',
                    width: rect.width + 'px',
                    zIndex: 9999,
                    ...(above
                        ? { bottom: (window.innerHeight - rect.top + 4) + 'px', top: 'auto' }
                        : { top: (rect.bottom + 4) + 'px', bottom: 'auto' }
                    )
                };
            }
            this.open = !this.open;
            if (!this.open) this.search = '';
        },

        choose(val) {
            this.value = String(val);
            $wire.set('{{ $wire }}', val);
            this.open = false;
            this.search = '';
        },

        clear() {
            this.value = '';
            $wire.set('{{ $wire }}', '');
        }
    }"
    @click.outside="open = false; search = ''"
    @keydown.escape.window="open = false; search = ''"
    @scroll.window="open = false"
    class="relative"
>
    {{-- ── Trigger ── --}}
    <button
        x-ref="trigger"
        type="button"
        @click="toggle()"
        class="w-full flex items-center gap-2.5 pl-3 pr-2.5 h-[38px] bg-[#f8fafc] border-[1.5px] rounded-[8px] text-left text-[13px] transition-all focus:outline-none select-none {{ $invalid ? 'border-red-400 bg-red-50/50' : 'border-[#e2e8f0]' }}"
        :class="open ? 'border-blue-500 bg-white shadow-[0_0_0_3px_rgba(37,99,235,.09)]' : '{{ $invalid ? '' : 'hover:border-slate-300 hover:bg-white' }}'"
    >
        @if($icon)
            <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
        @endif

        <template x-if="selected">
            <div class="flex-1 flex items-center gap-2 min-w-0">
                <span class="truncate font-medium text-slate-700" x-text="selected.label"></span>
                <template x-if="selected.sub">
                    <span class="text-[11px] text-slate-400 truncate flex-shrink-0" x-text="selected.sub"></span>
                </template>
            </div>
        </template>

        <template x-if="!selected">
            <span class="flex-1 text-slate-400 font-normal">{{ $placeholder }}</span>
        </template>

        <template x-if="selected">
            <button
                type="button"
                @click.stop="clear()"
                class="w-5 h-5 rounded flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-colors flex-shrink-0"
            >
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </template>

        <svg class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-150"
             :class="open ? 'rotate-180 text-blue-500' : 'text-slate-400'"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- ── Dropdown (renderizado em fixed, escapa de overflow:hidden) ── --}}
    <template x-teleport="body">
        <div
            x-show="open"
            :style="dropStyle"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-y-95"
            x-transition:enter-end="opacity-100 scale-y-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-y-100"
            x-transition:leave-end="opacity-0 scale-y-95"
            @click.outside="open = false; search = ''"
            style="display:none"
            class="bg-white border border-slate-200 rounded-xl shadow-[0_8px_30px_rgba(0,0,0,.12)] overflow-hidden origin-top"
        >
            {{-- Busca --}}
            @if($searchable)
            <div class="px-3 pt-2.5 pb-1.5 border-b border-slate-100">
                <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-2.5 h-8 border border-slate-200 focus-within:border-blue-400 focus-within:bg-white transition-colors">
                    <svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input
                        x-model="search"
                        x-ref="searchInput"
                        x-effect="if(open) $nextTick(() => $refs.searchInput?.focus())"
                        type="text"
                        placeholder="Buscar..."
                        class="flex-1 bg-transparent border-none outline-none text-xs text-slate-700 placeholder-slate-400 py-0"
                        @click.stop
                    />
                    <button x-show="search" @click.stop="search = ''" type="button" class="text-slate-400 hover:text-slate-600">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            {{-- Opções --}}
            <div class="max-h-52 overflow-y-auto py-1.5">
                <template x-if="filtered.length === 0">
                    <p class="px-4 py-3 text-xs text-slate-400 text-center">Nenhuma opção encontrada</p>
                </template>

                <template x-for="opt in filtered" :key="opt.value">
                    <button
                        type="button"
                        @click="choose(opt.value)"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-left transition-colors"
                        :class="String(value) === String(opt.value)
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50'"
                    >
                        {{-- Ícone da opção ou checkmark se selecionado --}}
                        <div class="w-4 h-4 flex items-center justify-center flex-shrink-0">
                            <template x-if="String(value) === String(opt.value)">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            @if($icon)
                            <template x-if="String(value) !== String(opt.value)">
                                <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 text-slate-300" />
                            </template>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="leading-snug truncate"
                               :class="String(value) === String(opt.value) ? 'font-semibold' : 'font-medium'"
                               x-text="opt.label"></p>
                            <template x-if="opt.sub">
                                <p class="text-[11px] text-slate-400 truncate" x-text="opt.sub"></p>
                            </template>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
