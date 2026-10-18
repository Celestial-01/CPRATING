@extends('layouts.admin')

@section('title', 'เพิ่มรายวิชา')

@section('content')
    <x-admin.page
        title="เพิ่มรายวิชา"
        description="กรอกข้อมูลรายวิชาและเลือกข้อมูลที่เกี่ยวข้อง"
    >
        <x-admin.form-card
            :action="route('admin.courses.store')"
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
                    :url="route('admin.teachers.search')"
                    add-label="+ เพิ่มอาจารย์"
                    placeholder="พิมพ์ชื่ออาจารย์เพื่อค้นหา"
                    hint="หากไม่เลือก ระบบจะแสดง “ไม่ระบุชื่ออาจารย์ผู้สอน”"
                    label-keys="prefix,teacher_name"
                    selected-label="display_name"
                    :selected="$selectedTeachers"
                />
            </x-admin.field>

            <x-admin.field label="หัวข้อที่เกี่ยวข้อง">
                <x-admin.tag-picker
                    name="topic_ids"
                    :url="route('admin.topics.search')"
                    add-label="+ เพิ่มหัวข้อ"
                    placeholder="พิมพ์ชื่อหัวข้อเพื่อค้นหา"
                    hint="หากไม่เลือก จะไม่มีหัวข้อแสดงในรายวิชานี้"
                    label-keys="name"
                    :selected="$selectedTopics"
                    :allow-create="true"
                />
            </x-admin.field>

            <x-admin.field label="วิชาบังคับก่อน (Previous Course)">
                <p class="mb-3 text-sm text-slate-500">
                    ค้นหาด้วยรหัสวิชาหรือชื่อวิชาภาษาอังกฤษ
                    ระบบจะแสดง Next Course ให้อัตโนมัติ
                </p>

                <x-admin.tag-picker
                    name="prerequisite_ids"
                    :url="route('admin.courses.search')"
                    placeholder="ค้นหารหัสวิชา หรือชื่อวิชาภาษาอังกฤษ..."
                    label-keys="course_code"
                    sub-keys="course_name_en"
                    selected-label="course_code"
                    selected-sub-label="course_name_en"
                    :selected="$selectedPrerequisites"
                    always-open
                />

                <p class="mt-2 text-sm text-slate-400">
                    เลือกได้หลายวิชา กด × บนแท็กเพื่อนำวิชาออก
                </p>
            </x-admin.field>
        </x-admin.form-card>
    </x-admin.page>
   
@endsection
