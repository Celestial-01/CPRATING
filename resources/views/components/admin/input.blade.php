@props(['name' => null, 'value' => null])

<input
    @if ($name) id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}" @endif
    {{ $attributes->merge([
        'type' => 'text',
        'class' => 'h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100',
    ]) }}
>