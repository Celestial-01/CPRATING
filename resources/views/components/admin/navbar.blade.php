@php
    $menus = [
        [
            'label'  => 'หน้าหลัก',
            'route'  => 'admin.dashboard',
            'active' => request()->routeIs('admin.dashboard'),
        ],
        [
            'label'  => 'เพิ่มรายวิชา',
            'route'  => 'courses.create',
            'active' => request()->routeIs('courses.create'),
        ],
        [
            'label'  => 'เพิ่มอาจารย์',
            'route'  => 'teachers.index',
            'active' => request()->routeIs('teachers.index'),
        ],
        [
            'label'  => 'เพิ่มหัวข้อ',
            'route'  => 'topics.index',
            'active' => request()->routeIs('topics.index'),
        ],
    ];
@endphp

<header class="relative z-50 mx-auto w-full max-w-[1120px] bg-white px-6 pb-3 pt-8">
    <nav
        class="flex min-h-[66px] items-center gap-6 rounded-[20px] bg-white px-5 py-3 shadow-[0_8px_18px_rgba(15,23,42,0.14)]"
        aria-label="เมนูผู้ดูแลระบบ"
    >
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex shrink-0 items-center gap-3 text-sm font-medium text-slate-900"
            aria-label="CPRATING Admin"
        >
            <span class="grid size-10 place-items-center rounded-full bg-[#d9d9d9] text-[10px] text-slate-700">LOGO</span>
            
        </a>

        <div class="flex items-center gap-7 text-sm">
            @foreach ($menus as $menu)
                <a
                    href="{{ route($menu['route']) }}"
                    @class([
                        'border-b-2 border-[#1684e5] pb-1 text-[#1684e5]' => $menu['active'],
                        'text-slate-900 transition hover:text-[#1684e5]' => ! $menu['active'],
                    ])
                >
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </div>

        <span class="ml-auto rounded-full bg-sky-50 px-3 py-1 text-[11px] font-medium text-[#1684e5]">
            ผู้ดูแลระบบ
        </span>
    </nav>
</header>