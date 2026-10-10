@extends('layouts.admin')

@section('title', isset($teacher) ? 'แก้ไขอาจารย์' : 'เพิ่มอาจารย์')

@section('content')

    <x-admin.page
        :title="isset($teacher) ? 'แก้ไขอาจารย์' : 'เพิ่มอาจารย์'"
        :description="isset($teacher) ? 'แก้ไขข้อมูลอาจารย์ผู้สอน' : 'กรอกข้อมูลอาจารย์ผู้สอน'"
    >
        <x-admin.form-card
            :action="isset($teacher) ? route('teachers.update', $teacher) : route('teachers.store')"
            :cancel="route('teachers.index')"
            :submit="isset($teacher) ? 'บันทึกการแก้ไข' : 'บันทึกอาจารย์'"
        >
            @isset($teacher)
                @method('PUT')
            @endisset

            <x-admin.field label="คำนำหน้า" name="prefix">
                <x-admin.select name="prefix">
                    <option value="">-- เลือกคำนำหน้า --</option>

                    @foreach (['อ.', 'ดร.', 'ผศ.', 'ผศ.ดร.', 'รศ.', 'รศ.ดร.', 'ศ.', 'ศ.ดร.'] as $prefix)
                        <option
                            value="{{ $prefix }}"
                            @selected(old('prefix', $teacher->prefix ?? '') === $prefix)
                        >
                            {{ $prefix }}
                        </option>
                    @endforeach
                </x-admin.select>
            </x-admin.field>

            <x-admin.field label="ชื่อและนามสกุลอาจารย์" name="teacher_name" required>
                <x-admin.input
                    name="teacher_name"
                    placeholder="กรอกชื่อและนามสกุลอาจารย์"
                    :value="old('teacher_name', $teacher->teacher_name ?? '')"
                    required
                />
            </x-admin.field>
        </x-admin.form-card>
    </x-admin.page>
@endsection
