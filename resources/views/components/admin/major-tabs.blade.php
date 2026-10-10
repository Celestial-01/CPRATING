@props(['majors', 'current', 'keyword' => '', 'types' => []])

<nav class="mt-6 grid grid-cols-5 gap-4" aria-label="เลือกสาขา">
    @foreach ($majors as $code => $name)
        <a
            href="{{ route('admin.dashboard', array_filter(['major' => $code, 'q' => $keyword, 'types' => $types])) }}"
            @class([
                'flex h-11 items-center justify-center rounded-[10px] text-base font-semibold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-sky-100',
                'bg-[#1684e5] text-white' => $code === $current,
                'bg-white text-slate-700 hover:text-[#1684e5]' => $code !== $current,
            ])
            @if ($code === $current) aria-current="page" @endif
        >
            {{ $code }}
        </a>
    @endforeach
</nav>