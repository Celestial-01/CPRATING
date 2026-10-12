@extends('layouts.admin')

@section('title', 'รายชื่ออาจารย์')

@section('content')
    <x-admin.page
        title="รายชื่ออาจารย์"
        description="จัดการข้อมูลอาจารย์ในระบบ CPRATING"
    >
        <div class="mb-5 flex justify-start">
            <a
                href="{{ route('admin.teachers.create') }}"
                class="inline-flex h-[45px] items-center rounded-[15px] bg-gradient-to-r from-[#1684df] to-[#56afff] px-6 text-sm text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-4 focus:ring-sky-200"
            >
                + เพิ่มอาจารย์
            </a>
        </div>

        <section class="overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm">
            @if ($teachers->isEmpty())
                <p class="px-6 py-16 text-center text-sm text-slate-400">
                    ไม่มีรายชื่ออาจารย์
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
                                            <a
                                                href="{{ route('admin.teachers.edit', $teacher) }}"
                                                class="inline-flex h-9 items-center rounded-full bg-[#e9f5ff] px-4 text-sm font-medium text-[#1684e5] transition hover:bg-[#d4ebff] focus:outline-none focus:ring-4 focus:ring-sky-100"
                                            >
                                                แก้ไข
                                            </a>

                                            <form
                                                action="{{ route('admin.teachers.destroy', $teacher) }}"
                                                method="POST"
                                                onsubmit="return confirm('ต้องการลบข้อมูลอาจารย์คนนี้หรือไม่?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex h-9 cursor-pointer items-center rounded-full bg-red-50 px-4 text-sm font-medium text-red-600 transition hover:bg-red-100 focus:outline-none focus:ring-4 focus:ring-red-100"
                                                >
                                                    ลบ
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
