@props([
    'name' => '',
    'placeholder' => 'Pilih...',
    'options' => [],
    'selected' => '',
    'required' => false,
    'search' => true,
    'xModel' => null,
])

@php
    $id = 'sel-' . \Illuminate\Support\Str::random(6);
    $optionsJson = json_encode(
        collect($options)->map(fn($label, $value) => ['value' => (string) $value, 'label' => $label])->values()
    );
@endphp

<div
    x-data="customSelect_{{ $id }}({
        name: '{{ $name }}',
        placeholder: '{{ $placeholder }}',
        selected: '{{ $selected }}',
        required: {{ $required ? 'true' : 'false' }},
        search: {{ $search ? 'true' : 'false' }},
        options: {{ $optionsJson }},
    })"
    class="relative"
>
    <input type="hidden" name="{{ $name }}" x-model="modelValue">

    <button type="button" @click="toggle()"
            class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm border border-gray-300 rounded-lg bg-white shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-150"
            :class="modelValue ? 'text-gray-900' : 'text-gray-400'">
        <span class="truncate" x-text="label"></span>
        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
        <div x-show="search" class="p-2 border-b border-gray-100">
            <input type="text" x-model="q" placeholder="Cari..."
                   class="w-full px-2.5 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
        <div class="max-h-56 overflow-y-auto">
            <template x-for="(opt, i) in filteredOptions" :key="i">
                <button type="button" @click="select(opt.value)"
                        class="w-full text-left px-3 py-2.5 text-sm transition-colors duration-100 flex items-center justify-between"
                        :class="opt.value === modelValue ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50'">
                    <span x-text="opt.label"></span>
                    <svg x-show="opt.value === modelValue" class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </template>
            <div x-show="filteredOptions.length === 0"
                 class="px-3 py-6 text-sm text-gray-400 text-center">
                Tidak ada data
            </div>
        </div>
    </div>
</div>

<script>
    function customSelect_{{ $id }}(config) {
        return {
            open: false,
            q: '',
            name: config.name,
            modelValue: config.selected || '',
            placeholder: config.placeholder,
            required: config.required,
            search: config.search,
            options: config.options,
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
                if (this.open) this.q = '';
            },
            select(value) {
                this.modelValue = value;
                this.open = false;
                this.q = '';
            }
        }
    }
</script>
