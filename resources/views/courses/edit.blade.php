@extends('layouts.admin')

@section('title', 'แก้ไขรายวิชา ' . $course->course_code)

@section('content')
    @php
        $teacherTags = $course->teachers
            ->map(fn ($t) => ['id' => $t->id, 'label' => trim($t->prefix . ' ' . $t->teacher_name)])
            ->values()->all();

        $topicTags = $course->topics
            ->map(fn ($t) => ['id' => $t->id, 'label' => $t->name])
            ->values()->all();

        $typeOptions = array_combine(\App\Models\Course::TYPES, \App\Models\Course::TYPES);
        $chip = 'inline-flex items-center rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]';
        $editBtn = 'rounded-full bg-sky-50 px-4 py-1.5 text-sm font-medium text-[#1684e5] transition hover:bg-[#d4ebff]';
        $saveBtn = 'h-9 rounded-full bg-[#1684e5] px-5 text-sm font-medium text-white transition hover:bg-[#0e73cc]';
        $cancelBtn = 'h-9 rounded-full border border-slate-200 px-5 text-sm text-slate-600 transition hover:bg-slate-50';
    @endphp

    <main class="mx-auto w-full max-w-[1120px] px-6 pb-10" data-csrf="{{ csrf_token() }}">
        <a href="{{ route('admin.dashboard', ['major' => $course->major]) }}"
           class="mt-8 inline-block text-sm text-slate-500 transition hover:text-[#1684e5]">
            ← กลับไปหน้ารายวิชา
        </a>

        {{-- ส่วนหัวรายวิชา --}}
        <section class="mt-4 rounded-[24px] border border-slate-200 bg-white p-4 md:p-7" aria-labelledby="course-title">
            <div class="grid gap-7 md:grid-cols-[minmax(0,0.44fr)_minmax(0,1fr)]">
                {{-- รูปปก --}}
                <div>
                    <div data-image-box data-url="{{ route('courses.image', $course) }}"
                         class="relative aspect-[300/313] overflow-hidden rounded-[16px] bg-gradient-to-br from-slate-600 to-slate-800">
                        <img data-course-image
                             src="{{ $course->image ? asset('storage/' . $course->image) : '' }}"
                             alt="ภาพประกอบวิชา {{ $course->course_name_th }}"
                             class="absolute inset-0 size-full object-cover {{ $course->image ? '' : 'hidden' }}">
                        <label class="absolute bottom-3 right-3 cursor-pointer rounded-full bg-white/95 px-4 py-2 text-xs font-medium text-[#1684e5] shadow transition hover:bg-white">
                            <span data-image-label>เปลี่ยนรูปปก</span>
                            <input type="file" accept="image/*" data-image-input class="sr-only">
                        </label>
                    </div>
                    <p data-image-error class="mt-2 hidden text-sm text-red-600"></p>
                    <p class="mt-2 text-xs text-slate-400">รองรับไฟล์รูปภาพ ขนาดไม่เกิน 2 MB</p>
                </div>

                {{-- ข้อมูลหลัก --}}
                <div class="flex min-w-0 flex-col gap-5">
                    <x-admin.inline-field :course="$course" field="course_name_th" label="ชื่อวิชาภาษาไทย"
                        display-class="text-2xl font-bold leading-snug text-black" id="course-title" />

                    <x-admin.inline-field :course="$course" field="course_name_en" label="ชื่อวิชาภาษาอังกฤษ"
                        display-class="text-base font-bold text-[#7c8ca4]" />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-admin.inline-field :course="$course" field="course_code" label="รหัสวิชา" />
                        <x-admin.inline-field :course="$course" field="credits" type="number" label="หน่วยกิต" suffix="หน่วยกิต" />
                        <x-admin.inline-field :course="$course" field="major" type="select" label="สาขาวิชา"
                            :options="\App\Models\Course::MAJORS" />
                        <x-admin.inline-field :course="$course" field="course_type" type="select" label="ประเภทวิชา"
                            :options="$typeOptions" />
                        <x-admin.inline-field :course="$course" field="is_active" type="select" label="สถานะ"
                            :options="[1 => 'เปิดสอน', 0 => 'ยังไม่เปิดสอน']" />
                    </div>

                    <div class="rounded-[16px] border-2 border-[#edf1f6] bg-[#f8fafc] px-4 py-3">
                        <x-admin.inline-field :course="$course" field="description" type="textarea"
                            label="คำอธิบายรายวิชา" placeholder="ยังไม่มีคำอธิบายรายวิชา"
                            display-class="text-sm leading-7 text-[#5e6d84]" />
                    </div>
                </div>
            </div>
        </section>

        {{-- ค่าเฉลี่ย + โอกาสได้เกรด (รอข้อมูลจากฝั่ง user) --}}
        <x-admin.course-stats />

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            {{-- อาจารย์ --}}
            <section data-inline-relation data-field="teacher_ids"
                     data-url="{{ route('courses.inline-update', $course) }}"
                     data-empty-text="ไม่ระบุชื่ออาจารย์ผู้สอน"
                     class="rounded-[22px] border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">อาจารย์ผู้สอน</h2>
                    <button type="button" data-edit class="{{ $editBtn }}">แก้ไข</button>
                </div>

                <div data-view class="mt-4">
                    <div data-chips class="flex flex-wrap gap-2">
                        @forelse ($teacherTags as $item)
                            <span class="{{ $chip }}">{{ $item['label'] }}</span>
                        @empty
                            <p class="text-sm text-slate-400">ไม่ระบุชื่ออาจารย์ผู้สอน</p>
                        @endforelse
                    </div>
                </div>

                <div data-editor class="mt-4 hidden">
                    <x-admin.tag-picker name="teacher_ids" :url="route('teachers.search')"
                        add-label="+ เพิ่มอาจารย์" placeholder="พิมพ์ชื่ออาจารย์เพื่อค้นหา"
                        :selected="$teacherTags" />
                    <p data-error class="mt-2 hidden text-sm text-red-600"></p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" data-save class="{{ $saveBtn }}">บันทึก</button>
                        <button type="button" data-cancel class="{{ $cancelBtn }}">ยกเลิก</button>
                    </div>
                </div>
            </section>

            {{-- หัวข้อ --}}
            <section data-inline-relation data-field="topic_ids"
                     data-url="{{ route('courses.inline-update', $course) }}"
                     data-empty-text="ยังไม่มีหัวข้อในรายวิชานี้"
                     class="rounded-[22px] border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">หัวข้อที่เกี่ยวข้อง</h2>
                    <button type="button" data-edit class="{{ $editBtn }}">แก้ไข</button>
                </div>

                <div data-view class="mt-4">
                    <div data-chips class="flex flex-wrap gap-2">
                        @forelse ($topicTags as $item)
                            <span class="{{ $chip }}">{{ $item['label'] }}</span>
                        @empty
                            <p class="text-sm text-slate-400">ยังไม่มีหัวข้อในรายวิชานี้</p>
                        @endforelse
                    </div>
                </div>

                <div data-editor class="mt-4 hidden">
                    <x-admin.tag-picker name="topic_ids" :url="route('topics.search')"
                        add-label="+ เพิ่มหัวข้อ" placeholder="พิมพ์ชื่อหัวข้อเพื่อค้นหา"
                        :selected="$topicTags" />
                    <p data-error class="mt-2 hidden text-sm text-red-600"></p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" data-save class="{{ $saveBtn }}">บันทึก</button>
                        <button type="button" data-cancel class="{{ $cancelBtn }}">ยกเลิก</button>
                    </div>
                </div>
            </section>
        </div>

        {{-- วิชาบังคับก่อน --}}
        <section data-inline-relation data-field="prerequisite_ids"
                 data-url="{{ route('courses.inline-update', $course) }}"
                 data-empty-text="ไม่มีวิชาบังคับก่อน"
                 class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">วิชาบังคับก่อน (Previous Course)</h2>
                <button type="button" data-edit class="{{ $editBtn }}">แก้ไข</button>
            </div>

            <div data-view class="mt-4">
                <div data-chips class="flex flex-wrap gap-2">
                    @forelse ($course->previousCourses as $pre)
                        <span class="{{ $chip }}">{{ $pre->course_code }} {{ $pre->course_name_en }}</span>
                    @empty
                        <p class="text-sm text-slate-400">ไม่มีวิชาบังคับก่อน</p>
                    @endforelse
                </div>
            </div>

            <div data-editor class="mt-4 hidden">
                <div id="prerequisite-picker" data-url="{{ route('courses.search') }}" class="relative">
                    <div id="selected-prerequisites" class="mb-3 flex flex-wrap gap-2">
                        @foreach ($course->previousCourses as $pre)
                            <span data-prerequisite-tag data-id="{{ $pre->id }}"
                                  class="inline-flex items-center gap-3 rounded-xl bg-[#e9f5ff] px-3 py-2">
                                <span class="flex flex-col gap-0.5">
                                    <span class="text-sm font-semibold text-[#1684e5]">{{ $pre->course_code }}</span>
                                    <span class="text-xs text-[#5792bd]">{{ $pre->course_name_en }}</span>
                                </span>
                                <input type="hidden" name="prerequisite_ids[]" value="{{ $pre->id }}">
                                <button type="button" data-remove-prerequisite aria-label="นำวิชาบังคับก่อนออก"
                                        class="text-xl leading-none text-[#1684e5] hover:text-red-500">×</button>
                            </span>
                        @endforeach
                    </div>

                    <input id="prerequisite-search" type="text" autocomplete="off"
                           placeholder="ค้นหารหัสวิชา หรือชื่อวิชาภาษาอังกฤษ..."
                           class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                    <div id="prerequisite-results"
                         class="absolute z-20 mt-1 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg"></div>
                </div>

                <p data-error class="mt-2 hidden text-sm text-red-600"></p>
                <div class="mt-4 flex gap-2">
                    <button type="button" data-save class="{{ $saveBtn }}">บันทึก</button>
                    <button type="button" data-cancel class="{{ $cancelBtn }}">ยกเลิก</button>
                </div>
            </div>
        </section>

        {{-- Next Course (อ่านอย่างเดียว คำนวณอัตโนมัติ) --}}
        <section class="mt-6 rounded-[22px] border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-slate-900">วิชาที่ต้องเรียนต่อ (Next Course)</h2>
            <p class="mt-1 text-xs text-slate-400">ระบบสร้างให้อัตโนมัติจากวิชาอื่นที่เลือกวิชานี้เป็นวิชาบังคับก่อน</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($course->nextCourses as $next)
                    <a href="{{ route('courses.edit', $next) }}"
                       class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700 transition hover:bg-slate-200">
                        {{ $next->course_code }} {{ $next->course_name_en }}
                    </a>
                @empty
                    <p class="text-sm text-slate-400">ยังไม่มีวิชาที่ต่อเนื่อง</p>
                @endforelse
            </div>
        </section>

        {{-- คอมเมนต์ (รอข้อมูลจากฝั่ง user) --}}
        <x-admin.course-comments />
    </main>
@endsection