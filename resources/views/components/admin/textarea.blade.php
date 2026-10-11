@props(['name', 'value' => null])

<textarea
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 py-3 text-sm outline-none transition placeholder:text-[#a8a8a8] focus:border-sky-500 focus:ring-4 focus:ring-sky-100',
    ]) }}
>{{ old($name, $value) }}</textarea>