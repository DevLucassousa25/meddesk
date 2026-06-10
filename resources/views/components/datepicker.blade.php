{{--
    Flatpickr datepicker synced with Livewire 3 via $wire.set().

    Usage:
        <x-datepicker wire="data_nascimento" placeholder="dd/mm/aaaa" />
        <x-datepicker wire="inicio_plano" :min-date="today" />
--}}
@props([
    'wire'        => '',
    'placeholder' => 'dd/mm/aaaa',
    'minDate'     => null,
    'maxDate'     => null,
    'invalid'     => false,
])

{{-- wire:ignore keeps Livewire from destroying Flatpickr on re-renders --}}
<div
    wire:ignore
    x-data="{
        fp: null,
        prop: '{{ $wire }}',
        init() {
            const self = this;
            this.fp = flatpickr(this.$el.querySelector('input'), {
                locale:      'pt',
                dateFormat:  'Y-m-d',      // value sent to Livewire (ISO)
                altInput:    true,
                altFormat:   'd/m/Y',      // display format for the user
                allowInput:  true,
                disableMobile: true,
                @if($minDate) minDate: '{{ $minDate }}', @endif
                @if($maxDate) maxDate: '{{ $maxDate }}', @endif

                onReady(dates, str, fp) {
                    // Apply form-input styling to the alt (display) input
                    if (fp.altInput) {
                        fp.altInput.classList.add('form-input');
                        @if($invalid) fp.altInput.classList.add('is-invalid'); @endif
                        fp.altInput.placeholder = '{{ $placeholder }}';
                        fp.altInput.removeAttribute('readonly');
                        fp.altInput.setAttribute('autocomplete', 'off');
                    }

                    // Set initial value from Livewire property
                    const initial = $wire[self.prop];
                    if (initial) fp.setDate(initial, false);
                },

                onChange(dates, str) {
                    $wire.set(self.prop, str || '');
                },

                onClear() {
                    $wire.set(self.prop, '');
                }
            });

            // Keep in sync if Livewire updates the property externally
            $wire.$watch(this.prop, v => {
                if (!v) this.fp.clear(false);
                else     this.fp.setDate(v, false);
            });
        }
    }"
>
    {{-- Flatpickr replaces this with an altInput; the original becomes hidden --}}
    <input
        type="text"
        placeholder="{{ $placeholder }}"
        class="form-input"
        autocomplete="off"
    />
</div>
