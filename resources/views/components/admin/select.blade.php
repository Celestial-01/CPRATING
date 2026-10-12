@props(['name' => null])

<select
    @if ($name) id="{{ $name }}" name="{{ $name }}" @endif
    {{ $attributes->merge([
        'class' => 'h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100',
    ]) }}
>
    {{ $slot }}
</select>
