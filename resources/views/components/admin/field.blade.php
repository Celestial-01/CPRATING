@props(['label', 'name' => null, 'required' => false])

<div>
    <label @if ($name) for="{{ $name }}" @endif class="mb-2 block text-sm font-medium text-slate-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    {{ $slot }}

    @if ($name)
        @error($name)
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>