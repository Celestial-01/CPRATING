@extends('layouts.admin')

@section('title', 'แก้ไขรายวิชา ' . $course->course_code)

@section('content')
    <main class="mx-auto w-full max-w-[1120px] px-6 pb-10" data-csrf="{{ csrf_token() }}">
        @if ($errors->has('course'))
            <p role="alert" class="mt-6 rounded-[15px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('course') }}
            </p>
        @endif
        <a href="{{ route('admin.dashboard', ['major' => $course->major]) }}"
           class="mt-8 inline-block text-sm text-slate-500 transition hover:text-[#1684e5]">
            ← กลับไปหน้ารายวิชา
        </a>

    
        <section class="mt-4 rounded-[24px] border border-slate-200 bg-white p-4 md:p-7" aria-labelledby="course-title">
            <div class="grid gap-7 md:grid-cols-[minmax(0,0.44fr)_minmax(0,1fr)]">
               
                <div>
                    <div
                        data-image-box
                        data-url="{{ route('admin.courses.image', $course) }}"
                        data-has-image="{{ $course->imageUrl() ? 'true' : 'false' }}"
                        data-uploading="false"
                        data-has-error="false"
                        class="group/cover"
                    >
                        <div class="relative aspect-[300/313] overflow-hidden rounded-[16px] bg-gradient-to-br from-slate-600 to-slate-800">
                            <img data-course-image
                                 src="{{ $course->imageUrl() ?? '' }}"
                                 alt="ภาพประกอบวิชา {{ $course->course_name_th }}"
                                 class="absolute inset-0 hidden size-full object-cover group-data-[has-image=true]/cover:block">
                            <label class="absolute bottom-3 right-3 cursor-pointer rounded-full bg-white/95 px-4 py-2 text-xs font-medium text-[#1684e5] shadow transition hover:bg-white">
                                <span class="group-data-[uploading=true]/cover:hidden">เปลี่ยนรูปปก</span>
                                <span class="hidden group-data-[uploading=true]/cover:inline">กำลังอัปโหลด...</span>
                                <input type="file" accept="image/*" data-image-input class="sr-only">
                            </label>
                        </div>
                        <p data-image-error class="mt-2 hidden text-sm text-red-600 group-data-[has-error=true]/cover:block"></p>
                    </div>

                    <p class="mt-2 text-xs text-slate-400">รองรับไฟล์รูปภาพ ขนาดไม่เกิน 2 MB</p>

                    <form
                        action="{{ route('admin.courses.destroy', $course) }}"
                        method="POST"
                        onsubmit="return confirm('ต้องการลบรายวิชานี้ใช่หรือไม่?')"
                        class="mt-4 flex justify-center">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            title="ลบรายวิชา"
                            class="inline-flex h-12 items-center justify-center rounded-full bg-red-50 px-8 text-base font-bold text-red-600 shadow-sm transition hover:bg-red-100"
                        >
                            ลบรายวิชา
                        </button>
                    </form>
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
                            :options="$majorOptions" />
                        <x-admin.inline-field :course="$course" field="course_type" type="select" label="ประเภทวิชา"
                            :options="$typeOptions" />
                        <x-admin.inline-field :course="$course" field="is_offered" type="select" label="สถานะ"
                            :options="$offeredOptions" />
                        <x-admin.inline-field :course="$course" field="publication_status" type="select" label="สถานะการเผยแพร่"
                            :options="$publicationOptions" />
                    </div>

                    <div class="rounded-[16px] border-2 border-[#edf1f6] bg-[#f8fafc] px-4 py-3">
                        <x-admin.inline-field :course="$course" field="description" type="textarea"
                            label="คำอธิบายรายวิชา" placeholder="ยังไม่มีคำอธิบายรายวิชา"
                            display-class="text-sm leading-7 text-[#5e6d84]" />
                    </div>
                </div>
            </div>
        </section>

        
        <x-admin.course-stats :course="$course" :grade-counts="$gradeCounts" />

        
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <x-admin.course-relation-field
                :course="$course"
                field="teacher_ids"
                title="อาจารย์ผู้สอน"
                empty-message="ไม่ระบุชื่ออาจารย์ผู้สอน"
                :selected="$course->teachers"
                :search-url="route('admin.teachers.search')"
                add-label="+ เพิ่มอาจารย์"
                placeholder="พิมพ์ชื่ออาจารย์เพื่อค้นหา"
                label-keys="prefix,teacher_name"
                selected-label="display_name"
            />

            <x-admin.course-relation-field
                :course="$course"
                field="topic_ids"
                title="หัวข้อที่เกี่ยวข้อง"
                empty-message="ยังไม่มีหัวข้อในรายวิชานี้"
                :selected="$course->topics"
                :search-url="route('admin.topics.search')"
                add-label="+ เพิ่มหัวข้อ"
                placeholder="พิมพ์ชื่อหัวข้อเพื่อค้นหา"
                label-keys="name"
                selected-label="name"
            />

            <x-admin.course-relation-field
                :course="$course"
                field="prerequisite_ids"
                title="วิชาบังคับก่อน (Previous Course)"
                empty-message="ไม่มีวิชาบังคับก่อน"
                :selected="$course->prerequisiteCourses"
                :search-url="route('admin.courses.search')"
                placeholder="ค้นหารหัสวิชา หรือชื่อวิชาภาษาอังกฤษ..."
                label-keys="course_code"
                sub-keys="course_name_en"
                selected-label="course_code"
                selected-sub-label="course_name_en"
                always-open
                wide
            />
        </div>

       
         <section
            class="mt-8 rounded-[22px] border border-slate-200 bg-white p-6 sm:p-8"
            aria-labelledby="prerequisites-title"
        >
            <p class="text-[11px] font-semibold tracking-[0.14em] text-slate-500">
                PREREQUISITES
            </p>

            <h2 id="prerequisites-title"
                class="mt-2 flex items-center gap-3 text-xl font-bold text-slate-950">
                <span class="h-7 w-1 rounded-full bg-[#2f80ed]"></span>
                เงื่อนไขก่อนลงทะเบียน
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                วิชาที่ควรผ่านมาก่อน และวิชาที่เปิดให้ลงต่อได้
            </p>

            <div class="mt-6 grid gap-7 md:grid-cols-2">
                {{-- วิชาบังคับก่อน --}}
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900">
                        <i data-lucide="lock" class="size-4 text-slate-500" aria-hidden="true"></i>
                        ต้องเรียนผ่านมาก่อน
                    </h3>

                    <div class="mt-4 space-y-3">
                        @forelse ($course->prerequisiteCourses as $previous)
                            <a
                                href="{{ route('admin.courses.edit', $previous) }}"
                                class="flex items-center justify-between gap-3 rounded-[16px] border border-slate-200 bg-slate-50 px-3 py-3 transition hover:border-blue-400 hover:bg-white"
                            >
                                <div class="min-w-0">
                                    <strong class="block text-sm text-slate-900">
                                        {{ $previous->course_code }}
                                    </strong>
                                    <span class="text-xs text-slate-500">
                                        {{ $previous->course_name_en }}
                                    </span>
                                </div>

                                <span class="shrink-0 rounded-full bg-black px-3 py-1 text-[10px] font-semibold text-white">
                                    บังคับ
                                </span>
                            </a>
                        @empty
                            <div class="flex min-h-20 items-center gap-3 rounded-[16px] border border-dashed border-slate-200 bg-slate-50/70 px-4 py-3 text-slate-400">
                                <i data-lucide="circle-check" class="size-5 shrink-0 text-emerald-400" aria-hidden="true"></i>
                                <p class="text-xs leading-5">
                                    ไม่มีวิชาที่ต้องผ่านก่อนลงทะเบียน
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- วิชาที่เรียนต่อได้ --}}
                <div>
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900">
                        <i data-lucide="book-open" class="size-4 text-slate-500" aria-hidden="true"></i>
                        ปลดล็อกวิชาต่อไป
                    </h3>

                    <div class="mt-4 space-y-3">
                        @forelse ($course->dependentCourses as $next)
                            <a
                                href="{{ route('admin.courses.edit', $next) }}"
                                class="group flex items-center justify-between gap-3 rounded-[16px] border border-slate-200 px-3 py-3 transition hover:-translate-y-0.5 hover:border-blue-400 hover:shadow-sm"
                            >
                                <div class="min-w-0">
                                    <strong class="block text-sm text-slate-900 transition-colors group-hover:text-blue-500">
                                        {{ $next->course_code }}
                                    </strong>
                                    <span class="text-xs text-slate-500">
                                        {{ $next->course_name_en }}
                                    </span>
                                </div>

                                <i data-lucide="arrow-up-right"
                                    class="size-4 shrink-0 text-slate-500 transition group-hover:text-blue-500"
                                    aria-hidden="true"></i>
                            </a>
                        @empty
                            <div class="flex min-h-20 items-center gap-3 rounded-[16px] border border-dashed border-slate-200 bg-slate-50/70 px-4 py-3 text-slate-400">
                                <i data-lucide="book-open" class="size-5 shrink-0" aria-hidden="true"></i>
                                <p class="text-xs leading-5">
                                    ยังไม่มีวิชาที่เปิดต่อจากรายวิชานี้
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>


        <x-admin.course-comments :course="$course" :comments="$comments" :sort="$commentSort" />
    </main>
@endsection
