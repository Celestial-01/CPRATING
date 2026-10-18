@extends('layouts.admin')

@section('title', 'รายการอาจารย์ที่ถูกลบ')

@section('content')
    <x-admin.page
        title="รายการอาจารย์ที่ถูกลบ"
        description="เรียกคืนหรือลบข้อมูลอาจารย์ออกจากระบบอย่างถาวร"
    >
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <a
                href="{{ route('admin.teachers.index') }}"
                class="inline-flex h-[45px] items-center rounded-[15px] border border-slate-200 bg-white px-6 text-sm font-medium text-slate-600 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
            >
                ← กลับไปรายชื่ออาจารย์
            </a>
        </div>

        <section class="overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm">
            @if ($teachers->isEmpty())
                <p class="px-6 py-16 text-center text-sm text-slate-400">
                    ไม่มีรายการอาจารย์ที่ถูกลบ
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#f8fafc] text-slate-600">
                            <tr>
                                <th class="px-6 py-4 font-medium">ลำดับ</th>
                                <th class="px-6 py-4 font-medium">คำนำหน้า</th>
                                <th class="px-6 py-4 font-medium">ชื่ออาจารย์</th>
                                <th class="px-6 py-4 text-right font-medium">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($teachers as $teacher)
                                <tr class="transition hover:bg-[#f8fafc]">
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4 text-slate-700">
                                        {{ $teacher->prefix ?: '-' }}
                                    </td>

                                    <td class="px-6 py-4 font-medium text-slate-900">
                                        {{ $teacher->teacher_name }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <form
                                                action="{{ route('admin.teachers.restore', $teacher->id) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="inline-flex h-9 cursor-pointer items-center rounded-full bg-[#e9f5ff] px-4 text-sm font-medium text-[#1684e5] transition hover:bg-[#d4ebff] focus:outline-none focus:ring-4 focus:ring-sky-100"
                                                >
                                                    กู้คืน
                                                </button>
                                            </form>

                                            <form
                                                action="{{ route('admin.teachers.force-delete', $teacher->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('ยืนยันลบข้อมูลอาจารย์คนนี้ถาวรหรือไม่? การลบนี้ไม่สามารถกู้คืนได้')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex h-9 cursor-pointer items-center rounded-full bg-red-50 px-4 text-sm font-medium text-red-600 transition hover:bg-red-100 focus:outline-none focus:ring-4 focus:ring-red-100"
                                                >
                                                    ลบถาวร
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </x-admin.page>
@endsection