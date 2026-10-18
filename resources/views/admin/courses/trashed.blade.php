
@extends('layouts.admin')

@section('title', 'รายการรายวิชาที่ถูกลบ')

@section('content')
    <main class="mx-auto w-full max-w-[1120px] px-6 pb-10">

        <header class="mt-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold leading-8 text-slate-900 sm:text-3xl">
                    รายการรายวิชาที่ถูกลบ
                </h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    ตรวจสอบรายวิชาที่ถูกลบ และเลือกกู้คืนหรือลบถาวร
                </p>
            </div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="inline-flex h-[42px] items-center gap-2 rounded-[12px]
                       border border-slate-200 bg-white px-4 text-sm
                       font-medium text-slate-600 shadow-sm transition
                       hover:bg-slate-50 hover:text-slate-900"
            >
                <span aria-hidden="true">←</span>
                กลับหน้ารายวิชา
            </a>
        </header>

        @if (session('success'))
            <div class="mt-6 rounded-[15px] border border-[#bfddfa]
                        bg-[#edf7ff] px-4 py-3 text-sm text-[#527ba8]">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-[15px] border border-red-200
                        bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="mt-8">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-lg font-medium text-slate-800">
                    รายวิชาที่ถูกลบ
                </h2>

                <span class="rounded-full bg-slate-100 px-3 py-1
                             text-sm text-slate-600">
                    {{ $courses->count() }} รายวิชา
                </span>
            </div>

            @if ($courses->isEmpty())
                <div class="rounded-[24px] border border-dashed
                            border-slate-200 bg-white px-6 py-14
                            text-center shadow-sm">
                    <div class="text-3xl text-slate-300" aria-hidden="true">
                        ♡
                    </div>

                    <h3 class="mt-3 font-medium text-slate-700">
                        ไม่มีรายวิชาที่ถูกลบ
                    </h3>

                    <p class="mt-1 text-sm text-slate-400">
                        รายวิชาที่ถูกลบจะปรากฏในหน้านี้
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2
                            lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($courses as $course)
                        <x-admin.course-card-delete :course="$course" />
                    @endforeach
                </div>
            @endif
        </section>

    </main>
@endsection
