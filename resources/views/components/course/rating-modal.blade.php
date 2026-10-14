@props(['course'])

<dialog
    class="course-rating-dialog m-auto w-[calc(100%-2rem)] max-w-[470px] rounded-[26px] border border-slate-200 bg-white p-0 text-slate-900 shadow-[0_24px_80px_rgba(15,23,42,0.2)]"
    data-course-rating-dialog
    aria-labelledby="course-rating-title"
    @if ($errors->any()) data-rating-errors @endif
>
    <form
        method="POST"
        action="{{ route('courses.rating.store', $course->course_code) }}"
        class="p-6 sm:p-8"
    >
        @csrf
        <header class="flex items-start justify-between gap-5">
            <div class="min-w-0">
                <span class="text-xs font-semibold tracking-[0.12em] text-[#2b7fff]">
                    {{ $course->course_code }}
                </span>
                <h2
                    id="course-rating-title"
                    class="mt-1 text-xl font-bold text-slate-950"
                >
                    ให้คะแนนวิชานี้
                </h2>
                <p class="mt-1 truncate text-sm text-slate-500">
                    {{ $course->course_name_th }}
                </p>
            </div>

            <button
                type="button"
                class="grid size-9 shrink-0 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100"
                data-course-rating-close
                aria-label="ปิดหน้าต่างให้คะแนน"
            >
                <i data-lucide="x" class="size-5" aria-hidden="true"></i>
            </button>
        </header>

        @if ($errors->any())
            <p class="mt-5 text-sm text-rose-600" role="alert">
                กรุณาให้คะแนนทั้งสามหัวข้อก่อนบันทึก
            </p>
        @endif

        <div class="mt-8 space-y-5">
            @foreach ([
                'difficulty' => 'ระดับความยาก',
                'workload' => 'ปริมาณงาน',
                'interest' => 'ความน่าสนใจของรายวิชานี้',
            ] as $ratingKey => $ratingLabel)
                <fieldset class="flex flex-col items-start gap-3 border-b border-slate-100 pb-5 last:border-b-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                    <legend class="text-sm font-semibold text-slate-800">
                        {{ $ratingLabel }}
                    </legend>

                    <div
                        class="flex shrink-0 items-center gap-1.5"
                        data-rating-group="{{ $ratingKey }}"
                        role="radiogroup"
                        aria-label="{{ $ratingLabel }}"
                    >
                        <input type="hidden" name="{{ $ratingKey }}" data-rating-input value="{{ old($ratingKey) }}">
                        @for ($rating = 1; $rating <= 5; $rating++)
                            <button
                                type="button"
                                class="course-rating-option grid size-9 place-items-center rounded-full border border-slate-200 text-slate-300 transition hover:border-[#2b7fff] hover:text-[#2b7fff] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100 sm:size-10"
                                data-rating-option
                                data-rating-value="{{ $rating }}"
                                role="radio"
                                aria-checked="false"
                                aria-label="{{ $rating }} จาก 5"
                            >
                                <i data-lucide="star" class="size-[18px]" aria-hidden="true"></i>
                            </button>
                        @endfor
                    </div>
                </fieldset>
            @endforeach
        </div>

        <button
            type="submit"
            class="mt-8 inline-flex h-12 w-full cursor-pointer items-center justify-center rounded-[16px] bg-[#2b7fff] px-4 text-sm font-semibold text-white transition hover:bg-[#2573e8] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400"
            data-course-rating-submit
            disabled
        >
            บันทึกคะแนน
        </button>
    </form>
</dialog>
