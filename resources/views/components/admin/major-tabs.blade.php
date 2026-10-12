<nav class="mt-6 grid grid-cols-5 gap-4" aria-label="เลือกสาขา">
    @foreach ($tabs as $tab)
        <a
            href="{{ $tab['url'] }}"
            class="{{ $tab['classes'] }}"
            @if ($tab['active']) aria-current="page" @endif
        >
            {{ $tab['code'] }}
        </a>
    @endforeach
</nav>
