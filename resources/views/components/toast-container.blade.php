{{--
    ┌─────────────────────────────────────────────────────────────────┐
    │  <x-toast-container />                                          │
    │                                                                 │
    │  Coloque uma vez no layout (app.blade.php).                     │
    │                                                                 │
    │  Disparo via Livewire (PHP):                                    │
    │    $this->dispatch('toast',                                     │
    │        type: 'success',   // success | info | warning | error   │
    │        title: 'Título',   // opcional                           │
    │        message: 'Texto',  // obrigatório                        │
    │        duration: 4000,    // ms, opcional (padrão 4000)         │
    │    );                                                           │
    │                                                                 │
    │  Disparo via JS:                                                │
    │    window.dispatchEvent(new CustomEvent('toast', {              │
    │        detail: { type: 'error', message: 'Algo deu errado' }   │
    │    }));                                                         │
    └─────────────────────────────────────────────────────────────────┘
--}}

{{-- Toasts de session (compatibilidade com flash existente) --}}
@if(session('success'))
    <div x-data x-init="
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type: 'success', message: {{ Js::from(session('success')) }} }
        }))
    "></div>
@endif
@if(session('error'))
    <div x-data x-init="
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type: 'error', message: {{ Js::from(session('error')) }} }
        }))
    "></div>
@endif
@if(session('warning'))
    <div x-data x-init="
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type: 'warning', message: {{ Js::from(session('warning')) }} }
        }))
    "></div>
@endif
@if(session('info'))
    <div x-data x-init="
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type: 'info', message: {{ Js::from(session('info')) }} }
        }))
    "></div>
@endif

{{-- Container principal --}}
<div
    x-data="medToast()"
    x-init="init()"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-2.5 pointer-events-none"
    style="width: 360px; max-width: calc(100vw - 2rem)"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-6 scale-95"
            class="pointer-events-auto bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden"
        >
            {{-- Barra colorida no topo --}}
            <div class="h-[3px] w-full" :class="barColor(toast.type)"></div>

            <div class="p-4">
                <div class="flex items-start gap-3">

                    {{-- Ícone --}}
                    <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center" :class="iconBg(toast.type)">
                        {{-- success --}}
                        <template x-if="toast.type === 'success'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" :class="iconColor(toast.type)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        {{-- info --}}
                        <template x-if="toast.type === 'info'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="18" height="18" :class="iconColor(toast.type)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
                            </svg>
                        </template>
                        {{-- warning --}}
                        <template x-if="toast.type === 'warning'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="18" height="18" :class="iconColor(toast.type)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </template>
                        {{-- error --}}
                        <template x-if="toast.type === 'error'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="18" height="18" :class="iconColor(toast.type)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </template>
                    </div>

                    {{-- Texto --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-sm font-bold text-slate-800 leading-snug" x-text="toast.title || typeLabel(toast.type)"></p>
                        <p class="text-sm text-slate-500 mt-0.5 leading-relaxed" x-text="toast.message" x-show="toast.message"></p>
                    </div>

                    {{-- Fechar --}}
                    <button
                        @click="remove(toast.id)"
                        class="flex-shrink-0 w-6 h-6 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-colors mt-0.5"
                    >
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Progress bar --}}
                <div class="mt-3 h-0.5 rounded-full bg-slate-100 overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all ease-linear"
                        :class="barColor(toast.type)"
                        :style="`width: ${toast.progress}%; transition-duration: ${toast.duration}ms`"
                        x-init="$nextTick(() => { toast.progress = 0 })"
                    ></div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function medToast() {
    return {
        toasts: [],

        init() {
            // Escuta evento nativo do browser
            window.addEventListener('toast', (e) => {
                this.add(e.detail || {});
            });

            // Escuta evento do Livewire 3
            if (window.Livewire) {
                window.Livewire.on('toast', (params) => {
                    this.add(Array.isArray(params) ? params[0] : params);
                });
            }
        },

        add({ type = 'success', title = '', message = '', duration = 4000 }) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, title, message, duration, progress: 100, visible: true });

            setTimeout(() => this.remove(id), duration);
        },

        remove(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        },

        barColor(type) {
            return {
                success: 'bg-emerald-500',
                info:    'bg-blue-500',
                warning: 'bg-amber-400',
                error:   'bg-red-500',
            }[type] ?? 'bg-slate-400';
        },

        iconBg(type) {
            return {
                success: 'bg-emerald-50',
                info:    'bg-blue-50',
                warning: 'bg-amber-50',
                error:   'bg-red-50',
            }[type] ?? 'bg-slate-100';
        },

        iconColor(type) {
            return {
                success: 'text-emerald-500',
                info:    'text-blue-500',
                warning: 'text-amber-500',
                error:   'text-red-500',
            }[type] ?? 'text-slate-500';
        },

        typeLabel(type) {
            return {
                success: 'Sucesso',
                info:    'Informação',
                warning: 'Atenção',
                error:   'Erro',
            }[type] ?? 'Notificação';
        },
    };
}
</script>
