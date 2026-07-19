@props([
    'name' => '',
    'placeholder' => 'Pilih...',
    'options' => [],
    'selected' => '',
    'required' => false,
    'search' => true,
    'xModel' => null,
    'error' => false,
])

@php
    $id = 'sel-' . \Illuminate\Support\Str::random(6);
    $optionsJson = json_encode(
        collect($options)->map(fn($label, $value) => ['value' => (string) $value, 'label' => $label])->values()
    );
    $totalOptions = count($options);
@endphp

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customSelect', (config) => ({
                open: false,
                q: '',
                name: config.name,
                modelValue: config.selected || '',
                placeholder: config.placeholder,
                required: config.required,
                search: config.search,
                showSearch: config.showSearch,
                options: config.options,
                hasError: config.error,
                get label() {
                    if (!this.modelValue) return this.placeholder;
                    const found = this.options.find(o => String(o.value) === String(this.modelValue));
                    return found ? found.label : this.placeholder;
                },
                get filteredOptions() {
                    if (!this.q) return this.options;
                    const q = this.q.toLowerCase();
                    return this.options.filter(o => o.label.toLowerCase().includes(q));
                },
                toggle() {
                    this.open = !this.open;
                    if (this.open) {
                        this.q = '';
                        this.$nextTick(() => {
                            if (this.showSearch && this.$refs.search) {
                                this.$refs.search.focus();
                            }
                        });
                    }
                },
                select(value) {
                    this.modelValue = value;
                    this.$refs.hiddenInput.value = value;
                    this.open = false;
                    this.q = '';
                    this.hasError = false;
                },
                setError(state) {
                    this.hasError = state;
                }
            }));
        });
    </script>
@endonce

<div
    x-data="customSelect({
        name: '{{ $name }}',
        placeholder: '{{ $placeholder }}',
        selected: '{{ $selected }}',
        required: {{ $required ? 'true' : 'false' }},
        search: {{ $search ? 'true' : 'false' }},
        showSearch: {{ $totalOptions > 8 ? 'true' : 'false' }},
        options: {{ $optionsJson }},
        error: {{ $error ? 'true' : 'false' }},
    })"
    x-init="$nextTick(() => {})"
    class="relative"
>
    <input type="hidden" name="{{ $name }}" :value="modelValue" x-ref="hiddenInput">

    <button type="button" @click="toggle()" @keydown.down.prevent="open = true; $nextTick(() => { if (showSearch) $refs.search?.focus() })"
            class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm border rounded-lg bg-white shadow-sm
                   focus:outline-none focus:ring-2 transition-all duration-150"
            :class="hasError
                ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500 hover:border-red-400 text-gray-900'
                : (modelValue ? 'border-gray-300 focus:ring-indigo-500/20 focus:border-indigo-500 hover:border-gray-400 text-gray-900' : 'border-gray-300 focus:ring-indigo-500/20 focus:border-indigo-500 hover:border-gray-400 text-gray-400')">
        <span class="truncate" x-html="label"></span>
        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
         @click.outside="open = false" @keydown.escape="open = false"
         class="absolute z-50 mt-1.5 w-full bg-white border border-gray-200 rounded-xl shadow-lg shadow-gray-200/50 overflow-hidden">
        <div x-show="showSearch" class="p-2 border-b border-gray-100 bg-indigo-50/30">
            <div class="relative">
                <svg class="w-4 h-4 text-indigo-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-ref="search" x-model="q" placeholder="Cari..."
                       class="w-full pl-8 pr-3 py-1.5 text-sm border border-indigo-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 font-medium">
                <div x-show="q" class="absolute inset-0 rounded-lg pointer-events-none transition-all duration-300"
                     :style="q ? 'box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); border-color: rgb(99, 102, 241); background-color: rgba(255, 255, 255, 0.8);' : ''"></div>
            </div>
        </div>
        <div class="max-h-56 overflow-y-auto overscroll-contain">
            <template x-for="(opt, i) in filteredOptions" :key="i">
                <button type="button" @click="select(opt.value)"
                        class="w-full text-left px-3 py-2.5 text-sm transition-all duration-100 flex items-center justify-between border-b border-gray-50 last:border-b-0"
                        :class="opt.value === modelValue ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-indigo-50/50 hover:text-gray-900'">
                    <span class="truncate" x-html="opt.label"></span>
                    <svg x-show="opt.value === modelValue" class="w-4 h-4 text-indigo-500 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </template>
            <div x-show="filteredOptions.length === 0"
                 class="px-3 py-8 text-sm text-gray-400 text-center">
                <svg class="w-6 h-6 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tidak ada data
            </div>
            <div x-show="!q && options.length === 0"
                 class="px-3 py-8 text-sm text-gray-400 text-center">
                <svg class="w-6 h-6 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                Tidak ada opsi
            </div>
        </div>
    </div>
</div>
