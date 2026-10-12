<dialog
    class="legal-dialog w-[calc(100%-2rem)] max-w-[520px] rounded-[18px] border border-slate-200 bg-white p-0 text-slate-800 shadow-[0_18px_45px_rgba(15,23,42,0.22)]"
    data-legal-modal
    aria-labelledby="legal-modal-title"
>
    <section
        class="flex max-h-[min(640px,calc(100vh-48px))] flex-col overflow-hidden py-6 pl-6 pr-4 sm:py-8 sm:pl-8 sm:pr-5"
        data-legal-modal-panel
    >
        <header class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-[#1684df]">CPRATING</p>
                <h2
                    id="legal-modal-title"
                    class="mt-1 text-xl font-semibold text-slate-900"
                    data-legal-modal-title
                ></h2>
            </div>
            <button
                type="button"
                class="grid size-9 shrink-0 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200"
                data-legal-modal-close
                aria-label="ปิดหน้าต่าง"
            >
                <i data-lucide="x" class="size-5" aria-hidden="true"></i>
            </button>
        </header>

        <div
            class="legal-modal-scroll mt-5 min-h-0 flex-1 space-y-4 overflow-y-auto pr-3 text-sm leading-7 text-slate-600"
            data-legal-modal-content
        ></div>
    </section>
</dialog>

<template data-legal-content="privacy" data-legal-title="นโยบายความเป็นส่วนตัว">
    <p>นโยบายนี้อธิบายวิธีที่ CPRATING เก็บ ใช้ และปกป้องข้อมูลของคุณเมื่อคุณใช้งานระบบ</p>
    <h3 class="pt-2 font-semibold text-slate-900">ข้อมูลที่เราเก็บ</h3>
    <p>เราอาจเก็บอีเมล ชื่อ ข้อมูลการเข้าสู่ระบบ และข้อมูลที่คุณส่งผ่านการรีวิว ความคิดเห็น หรือการบันทึกรายวิชา</p>
    <h3 class="pt-2 font-semibold text-slate-900">เราใช้ข้อมูลอย่างไร</h3>
    <p>ข้อมูลถูกใช้เพื่อสร้างและดูแลบัญชี ยืนยันตัวตน ส่งอีเมลที่จำเป็น ปรับปรุงบริการ และช่วยดูแลความปลอดภัยของชุมชน</p>
    <h3 class="pt-2 font-semibold text-slate-900">การเปิดเผยข้อมูล</h3>
    <p>เราจะไม่ขายหรือเผยแพร่ข้อมูลส่วนตัวของคุณโดยไม่ได้รับอนุญาต เว้นแต่จำเป็นตามกฎหมายหรือเพื่อป้องกันการใช้บริการในทางที่ผิด</p>
    <h3 class="pt-2 font-semibold text-slate-900">การเก็บรักษาและความปลอดภัย</h3>
    <p>เราจัดเก็บข้อมูลเท่าที่จำเป็นและใช้มาตรการที่เหมาะสมเพื่อป้องกันการเข้าถึง แก้ไข หรือเปิดเผยโดยไม่ได้รับอนุญาต</p>
    <h3 class="pt-2 font-semibold text-slate-900">สิทธิ์ของคุณ</h3>
    <p>คุณสามารถติดต่อผู้ดูแลระบบเพื่อขอเข้าถึง แก้ไข หรือลบบัญชีและข้อมูลส่วนตัวของคุณได้ตามเงื่อนไขที่เกี่ยวข้อง</p>
</template>

<template data-legal-content="terms" data-legal-title="ข้อกำหนดการใช้งาน">
    <p>เมื่อสมัครหรือใช้งาน CPRATING คุณตกลงปฏิบัติตามข้อกำหนดเหล่านี้และใช้บริการอย่างรับผิดชอบ</p>
    <h3 class="pt-2 font-semibold text-slate-900">การใช้งานที่ยอมรับได้</h3>
    <p>ใช้ระบบเพื่อการศึกษาและแลกเปลี่ยนข้อมูลรายวิชาอย่างสุจริต ไม่แอบอ้างตัวตน ไม่รบกวนการทำงานของระบบ และไม่ใช้ข้อมูลของผู้อื่นโดยไม่ได้รับอนุญาต</p>
    <h3 class="pt-2 font-semibold text-slate-900">เนื้อหาของผู้ใช้</h3>
    <p>คุณรับผิดชอบต่อรีวิว ความคิดเห็น และข้อมูลที่ส่งเข้าระบบ เนื้อหาต้องไม่เป็นเท็จ ละเมิดสิทธิ์ผู้อื่น คุกคาม เหยียดหยาม เป็นสแปม หรือผิดกฎหมาย</p>
    <h3 class="pt-2 font-semibold text-slate-900">การดูแลเนื้อหา</h3>
    <p>CPRATING อาจซ่อน แก้ไข หรือลบเนื้อหาที่ไม่เหมาะสม และจำกัดการเข้าถึงบัญชีที่ละเมิดข้อกำหนด โดยไม่จำเป็นต้องแจ้งล่วงหน้า</p>
    <h3 class="pt-2 font-semibold text-slate-900">บัญชีและความปลอดภัย</h3>
    <p>คุณต้องรักษาข้อมูลเข้าสู่ระบบของตัวเองให้ปลอดภัยและแจ้งผู้ดูแลระบบทันทีเมื่อสงสัยว่าบัญชีถูกใช้โดยไม่ได้รับอนุญาต</p>
    <h3 class="pt-2 font-semibold text-slate-900">การเปลี่ยนแปลงข้อกำหนด</h3>
    <p>เราอาจปรับปรุงข้อกำหนดเพื่อให้สอดคล้องกับการให้บริการ โดยจะแสดงฉบับล่าสุดในระบบเมื่อมีการเปลี่ยนแปลง</p>
</template>
