
@props(['course'])

<article
    class="group relative flex min-h-[190px] flex-col justify-between
           rounded-[20px] border border-slate-100 bg-white p-5
           shadow-sm transition-all duration-200
           hover:border-slate-200 hover:bg-slate-100 hover:shadow-md"
>
    <div>
        <p class="text-xs font-medium tracking-wide text-slate-400">
            {{ $course->course_code }}
        </p>

        <h3 class="mt-3 line-clamp-2 text-base font-semibold leading-6 text-slate-800">
            {{ $course->course_name_th }}
        </h3>

        @if ($course->course_name_en)
            <p class="mt-1 line-clamp-2 text-sm text-slate-500">
                {{ $course->course_name_en }}
            </p>
        @endif

        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
            @if ($course->major)
                <span class="rounded-full bg-slate-200/70 px-2.5 py-1">
                    {{ $course->major }}
                </span>
            @endif

            <span class="rounded-full bg-red-100 px-2.5 py-1 text-red-600">
                ถูกลบแล้ว
            </span>
        </div>
    </div>

    <div
        class="mt-5 flex flex-wrap gap-2
               opacity-100 transition-opacity duration-200
               sm:opacity-0 sm:group-hover:opacity-100
               sm:group-focus-within:opacity-100"
    >
        <form
            action="{{ route('admin.courses.restore', $course->id) }}"
            method="POST"
        >
            @csrf
            @method('PATCH')

            <button
                type="submit"
                class="rounded-[10px] bg-emerald-600 px-3 py-2
                       text-sm font-medium text-white transition
                       hover:bg-emerald-700"
            >
                กู้คืน
            </button>
        </form>
        
        <form
            action="{{ route('admin.courses.force-delete', $course->id) }}"
            method="POST"
            onsubmit="return confirm('ยืนยันลบรายวิชานี้ถาวรหรือไม่? ข้อมูลคะแนนและรีวิวของรายวิชานี้จะถูกลบด้วย และไม่สามารถกู้คืนได้')"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="rounded-[10px] bg-red-600 px-3 py-2
                    text-sm font-medium text-white transition
                    hover:bg-red-700"
            >
                ลบถาวร
            </button>
        </form>

    </div>
</article>
