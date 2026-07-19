@props(['active', 'icon'])

<a {{ $attributes->merge(['class' => 'flex items-center rounded-lg text-sm font-medium transition-all duration-150']) }}
   :class="{
       'bg-indigo-500/40 text-white': {{ $active ? 'true' : 'false' }},
       'text-indigo-200/80 hover:text-white hover:bg-indigo-500/20': {{ !$active ? 'true' : 'false' }},
       'justify-center w-12 h-12 mx-auto': $store.sidebar.collapsed,
       'gap-3 px-3 py-3': !$store.sidebar.collapsed
   }"
   @click="$store.sidebar.closeMobile()">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icon !!}
    </svg>
    <span x-show="!$store.sidebar.collapsed" x-cloak>{{ $slot }}</span>
</a>
