@props(['cards' => 6])

<section
    {{ $attributes->class('mt-8 sm:mt-10') }}
    aria-hidden="true"
>
    <div class="mb-5 flex items-center justify-between gap-4">
        <div class="h-8 w-56 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
        <div class="h-5 w-16 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
    </div>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-5">
        @for ($index = 0; $index < $cards; $index++)
            <x-skeleton.course-card />
        @endfor
    </div>
</section>
