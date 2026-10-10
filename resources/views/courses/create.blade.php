@extends('layouts.admin')

@section('title', 'เพิ่มรายวิชา')

@section('content')
    @php
        $selectedTeachers = $teachers->whereIn('id', old('teacher_ids', []))
            ->map(fn ($t) => ['id' => $t->id, 'label' => trim($t->prefix . ' ' . $t->teacher_name)])
            ->values()->all();

        $selectedTopics = $topics->whereIn('id', old('topic_ids', []))
            ->map(fn ($t) => ['id' => $t->id, 'label' => $t->name])
            ->values()->all();
    @endphp
    @php
    $selectedPrerequisites = $courses
            ->whereIn('id', old('prerequisite_ids', []))
            ->map(fn ($course) => [
                'id' => $course->id,
                'code' => $course->course_code,
                'name' => $course->course_name_en,
            ])
            ->values()
            ->all();
    @endphp
    <x-admin.page
        title="เพิ่มรายวิชา"
        description="กรอกข้อมูลรายวิชาและเลือกข้อมูลที่เกี่ยวข้อง"
    >
        <x-admin.form-card
            :action="route('courses.store')"
            :cancel="route('admin.dashboard')"
            :files="true"
            submit="บันทึกรายวิชา"
        >
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.field label="รหัสวิชา" name="course_code" required>
                    <x-admin.input name="course_code" maxlength="8" required />
                </x-admin.field>

                <x-admin.field label="หน่วยกิต" name="credits" required>
                    <x-admin.input name="credits" type="number" min="0" required />
                </x-admin.field>

                <x-admin.field label="ชื่อวิชาภาษาไทย" name="course_name_th" required>
                    <x-admin.input name="course_name_th" required />
                </x-admin.field>

                <x-admin.field label="ชื่อวิชาภาษาอังกฤษ" name="course_name_en" required>
                    <x-admin.input name="course_name_en" required />
                </x-admin.field>

                <x-admin.field label="สาขาวิชา" name="major" required>
                    <x-admin.select name="major" required>
                        <option value="">เลือกสาขาวิชา</option>
                        @foreach ($majors as $value => $label)
                            <option value="{{ $value }}" @selected(old('major') === $value)>{{ $label }}</option>
                        @endforeach
                    </x-admin.select>
                </x-admin.field>

                <x-admin.field label="ประเภทวิชา" name="course_type" required>
                    <x-admin.select name="course_type" required>
                        <option value="">เลือกประเภทวิชา</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(old('course_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </x-admin.select>
                </x-admin.field>
            </div>

            <x-admin.field label="คำอธิบายรายวิชา" name="description">
                <x-admin.textarea name="description" rows="4" />
            </x-admin.field>

            <x-admin.field label="รูปหน้าปกรายวิชา" name="image">
                <x-admin.input name="image" type="file" accept="image/*" class="h-auto py-2.5" />
                <p class="mt-1 text-xs text-slate-400">รองรับไฟล์รูปภาพ ขนาดไม่เกิน 2 MB</p>
            </x-admin.field>

            <x-admin.field label="อาจารย์ผู้สอน">
                <x-admin.tag-picker
                    name="teacher_ids"
                    :url="route('teachers.search')"
                    add-label="+ เพิ่มอาจารย์"
                    placeholder="พิมพ์ชื่ออาจารย์เพื่อค้นหา"
                    hint="หากไม่เลือก ระบบจะแสดง “ไม่ระบุชื่ออาจารย์ผู้สอน”"
                    :selected="$selectedTeachers"
                />
            </x-admin.field>

            <x-admin.field label="หัวข้อที่เกี่ยวข้อง">
                <x-admin.tag-picker
                    name="topic_ids"
                    :url="route('topics.search')"
                    add-label="+ เพิ่มหัวข้อ"
                    placeholder="พิมพ์ชื่อหัวข้อเพื่อค้นหา"
                    hint="หากไม่เลือก จะไม่มีหัวข้อแสดงในรายวิชานี้"
                    :selected="$selectedTopics"
                />
            </x-admin.field>

            <x-admin.field label="วิชาบังคับก่อน (Previous Course)">
                <p class="mb-3 text-sm text-slate-500">
                    ค้นหาด้วยรหัสวิชาหรือชื่อวิชาภาษาอังกฤษ
                    ระบบจะแสดง Next Course ให้อัตโนมัติ
                </p>

                <div
                    id="prerequisite-picker"
                    data-url="{{ route('courses.search') }}"
                    class="relative"
                >
                    {{-- วิชาที่เลือกแล้ว --}}
                    <div
                        id="selected-prerequisites"
                        class="mb-3 flex flex-wrap gap-2"
                    >
                        @foreach ($selectedPrerequisites as $item)
                            <span
                                data-prerequisite-tag
                                data-id="{{ $item['id'] }}"
                                class="inline-flex items-center gap-2 rounded-full bg-[#e9f5ff] px-3 py-1 text-sm font-medium text-[#1684e5]"
                            >
                                <span class="flex flex-col gap-0.5">
                                    <span class="text-sm font-semibold text-[#1684e5]">
                                        {{ $item['code'] }}
                                    </span>
                                    <span class="text-sm font-semibold text-[#1684e5]">
                                        {{ $item['name'] }}
                                    </span>
                                </span>

                                <input
                                    type="hidden"
                                    name="prerequisite_ids[]"
                                    value="{{ $item['id'] }}"
                                >

                                <button
                                    type="button"
                                    data-remove-prerequisite
                                    aria-label="นำวิชาบังคับก่อนออก"
                                    class="text-xl leading-none text-[#1684e5] hover:text-red-500"
                                >
                                    ×
                                </button>
                            </span>
                        @endforeach
                    </div>

                    <input
                        id="prerequisite-search"
                        type="text"
                        autocomplete="off"
                        placeholder="ค้นหารหัสวิชา หรือชื่อวิชาภาษาอังกฤษ..."
                        class="h-[45px] w-full rounded-[11px] border border-[#bcbcbc] bg-white px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                    >

                    <div
                        id="prerequisite-results"
                        class="absolute z-20 mt-1 hidden max-h-64 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg"
                    ></div>
                </div>

                <p class="mt-2 text-sm text-slate-400">
                    เลือกได้หลายวิชา กด × บนแท็กเพื่อนำวิชาออก
                </p>
            </x-admin.field>
        </x-admin.form-card>
    </x-admin.page>
@endsection