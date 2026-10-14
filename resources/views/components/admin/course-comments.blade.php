<section id="comments" class="mt-8 rounded-[22px] border border-slate-200 bg-white p-5 sm:p-6" aria-labelledby="comments-title">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 id="comments-title" class="flex items-center gap-2 text-lg font-semibold text-slate-900">
            <i data-lucide="message-circle" class="size-5 text-[#1684e5]" aria-hidden="true"></i>
            คอมเมนต์ <span class="text-sm font-normal text-slate-400">(0)</span>
        </h2>

        <div data-comment-sort class="inline-flex rounded-full bg-slate-100 p-1 text-sm">
            @foreach ($sortOptions as $sortOption)
                <button
                    type="button"
                    data-comment-sort-option
                    aria-pressed="{{ $sortOption['active'] ? 'true' : 'false' }}"
                    data-active="{{ $sortOption['active'] ? 'true' : 'false' }}"
                    class="rounded-full px-4 py-1.5 text-slate-500 transition data-[active=true]:bg-white data-[active=true]:font-medium data-[active=true]:text-[#1684e5] data-[active=true]:shadow-sm"
                >
                    {{ $sortOption['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ช่องเขียนคอมเมนต์ (ปิดไว้ก่อน) --}}
    <div class="mt-6 flex gap-3">
        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-slate-100 text-xs text-slate-400">ฉัน</span>
        <div class="relative flex-1">
            <textarea
                rows="3"
                disabled
                placeholder="ระบบคอมเมนต์จะเปิดใช้งานเมื่อฝั่งผู้ใช้พร้อม"
                class="w-full resize-none rounded-[14px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 placeholder:text-slate-400"
            ></textarea>
            <button type="button" disabled
                    class="absolute bottom-3 right-3 inline-flex h-8 items-center gap-1.5 rounded-full bg-slate-200 px-4 text-xs font-medium text-slate-400">
                <i data-lucide="send" class="size-3.5" aria-hidden="true"></i>
                ส่ง
            </button>
        </div>
    </div>

    <p class="mt-8 rounded-[14px] bg-slate-50 px-4 py-10 text-center text-sm text-slate-400">
        ยังไม่มีคอมเมนต์
    </p>
</section>
