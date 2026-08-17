@props(['type' => 'circle', 'color' => 'blue', 'class' => ''])

@php
    $classes = match ($color) {
        'red' => 'bg-red-50 text-red-600 ring-red-100 dark:bg-red-950 dark:text-red-300 dark:ring-red-900',
        'yellow' => 'bg-yellow-50 text-yellow-700 ring-yellow-100 dark:bg-yellow-950 dark:text-yellow-300 dark:ring-yellow-900',
        'blue' => 'bg-blue-50 text-blue-600 ring-blue-100 dark:bg-blue-950 dark:text-blue-300 dark:ring-blue-900',
        default => 'bg-blue-50 text-blue-600 ring-blue-100 dark:bg-blue-950 dark:text-blue-300 dark:ring-blue-900',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-2xl ring-1 '.$classes.' '.$class]) }} aria-hidden="true">
    <svg viewBox="0 0 24 24" class="h-1/2 w-1/2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        @switch($type)
            @case('square')
                <path d="M5 5h14v14H5z" />
                @break
            @case('triangle')
                <path d="M12 4 21 20H3L12 4Z" />
                @break
            @case('circle-hole')
                <circle cx="12" cy="12" r="8" />
                <circle cx="12" cy="12" r="3" />
                @break
            @default
                <circle cx="12" cy="12" r="8" />
        @endswitch
    </svg>
</span>
