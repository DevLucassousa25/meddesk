{{--
    ┌──────────────────────────────────────────────────────────────────────┐
    │  <x-confirm-modal />                                                 │
    │                                                                      │
    │  Coloque UMA vez no app.blade.php (já incluído).                     │
    │                                                                      │
    │  USO nos templates Alpine (dentro de Livewire):                      │
    │                                                                      │
    │  <!-- Delete -->                                                     │
    │  @click="$store.modal.confirm({                                      │
    │      type: 'delete',                                                 │
    │      title: 'Excluir paciente',                                      │
    │      message: 'Esta ação não pode ser desfeita.',                    │
    │      onConfirm: () => $wire.excluir({{ $id }})                       │
    │  })"                                                                 │
    │                                                                      │
    │  <!-- Warning -->                                                    │
    │  @click="$store.modal.confirm({                                      │
    │      type: 'warning',                                                │
    │      title: 'Inativar pacientes',                                    │
    │      message: 'Os pacientes selecionados serão inativados.',         │
    │      confirmText: 'Inativar',                                        │
    │      onConfirm: () => $wire.inativarSelecionados()                   │
    │  })"                                                                 │
    │                                                                      │
    │  <!-- Success (feedback) -->                                         │
    │  $store.modal.confirm({                                              │
    │      type: 'success',                                                │
    │      title: 'Salvo!',                                                │
    │      message: 'As alterações foram salvas.',                         │
    │      cancelText: null,  // oculta botão cancelar                     │
    │      confirmText: 'OK',                                              │
    │  })                                                                  │
    │                                                                      │
    │  Tipos: delete | error | warning | info | success                   │
    └──────────────────────────────────────────────────────────────────────┘
--}}

<div
    x-data
    x-show="$store.modal.open"
    x-cloak
    class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
    @keydown.escape.window="$store.modal.close()"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]"
        x-show="$store.modal.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.modal.close()"
    ></div>

    {{-- Card --}}
    <div
        class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden"
        x-show="$store.modal.open"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-90 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
    >
        {{-- Botão fechar --}}
        <button
            @click="$store.modal.close()"
            class="absolute top-4 right-4 w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors z-10"
        >
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>

        <div class="px-8 pt-10 pb-8 text-center">

            {{-- Ícone --}}
            <div class="flex justify-center mb-5">
                {{-- delete --}}
                <template x-if="$store.modal.type === 'delete' || $store.modal.type === 'error'">
                    <div class="w-16 h-16 rounded-2xl bg-red-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2" width="30" height="30">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </template>
                {{-- warning --}}
                <template x-if="$store.modal.type === 'warning'">
                    <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2" width="30" height="30">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                </template>
                {{-- success --}}
                <template x-if="$store.modal.type === 'success'">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2.5" width="30" height="30">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </template>
                {{-- info --}}
                <template x-if="$store.modal.type === 'info'">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2" width="30" height="30">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                        </svg>
                    </div>
                </template>
            </div>

            {{-- Título --}}
            <h3 class="text-lg font-bold text-slate-800 mb-2 leading-snug" x-text="$store.modal.title"></h3>

            {{-- Mensagem --}}
            <p class="text-sm text-slate-500 leading-relaxed mb-7" x-text="$store.modal.message" x-show="$store.modal.message"></p>

            {{-- Botões --}}
            <div class="flex flex-col gap-2.5">

                {{-- Confirm --}}
                <button
                    @click="$store.modal.execute()"
                    class="w-full h-11 rounded-2xl text-sm font-bold text-white transition-all active:scale-[.98]"
                    :class="{
                        'bg-red-500 hover:bg-red-600 shadow-red-200 shadow-md':   $store.modal.type === 'delete' || $store.modal.type === 'error',
                        'bg-amber-500 hover:bg-amber-600 shadow-amber-200 shadow-md': $store.modal.type === 'warning',
                        'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-200 shadow-md': $store.modal.type === 'success',
                        'bg-blue-500 hover:bg-blue-600 shadow-blue-200 shadow-md': $store.modal.type === 'info',
                    }"
                    x-text="$store.modal.confirmText"
                ></button>

                {{-- Cancel --}}
                <button
                    x-show="$store.modal.cancelText"
                    @click="$store.modal.close()"
                    class="w-full h-11 rounded-2xl text-sm font-semibold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors active:scale-[.98]"
                    x-text="$store.modal.cancelText"
                ></button>
            </div>
        </div>
    </div>
</div>

{{-- Alpine Store --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('modal', {
        open:        false,
        type:        'warning',
        title:       '',
        message:     '',
        confirmText: 'Confirmar',
        cancelText:  'Cancelar',
        _callback:   null,

        confirm(cfg = {}) {
            this.type        = cfg.type        ?? 'warning';
            this.title       = cfg.title       ?? '';
            this.message     = cfg.message     ?? '';
            this.confirmText = cfg.confirmText ?? 'Confirmar';
            this.cancelText  = cfg.cancelText  !== undefined ? cfg.cancelText : 'Cancelar';
            this._callback   = cfg.onConfirm   ?? null;
            this.open        = true;
        },

        // Atalhos semânticos
        delete(cfg)  { this.confirm({ type: 'delete',  confirmText: 'Excluir',   ...cfg }); },
        warning(cfg) { this.confirm({ type: 'warning', confirmText: 'Confirmar', ...cfg }); },
        success(cfg) { this.confirm({ type: 'success', cancelText: null,         ...cfg }); },
        info(cfg)    { this.confirm({ type: 'info',    confirmText: 'OK',        ...cfg }); },

        execute() {
            if (typeof this._callback === 'function') this._callback();
            this.close();
        },

        close() {
            this.open = false;
        },
    });

    // Atalho global
    window.$modal = Alpine.store('modal');
});
</script>

