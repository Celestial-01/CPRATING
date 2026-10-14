<footer class="bg-[#edf2f9] pt-9 pb-10 text-[13px] text-[#657083]">
    <div class="mx-auto w-full max-w-[1160px] px-6">
        <p class="max-w-[760px] border-b border-[#0c326421] pb-5 leading-[1.6]">
            ข้อมูลรายวิชาและแผนการเรียนแสดงตามที่เผยแพร่ในระบบ คะแนนและความคิดเห็นมาจากผู้ใช้จริง
        </p>

        <div class="grid grid-cols-[1.4fr_repeat(3,1fr)] gap-8 py-7 max-[980px]:grid-cols-2">
            <div>
                <b class="mb-2 block text-[13px] text-[#0b1220]">CPRATING</b>
                รีวิวรายวิชาก่อนลงทะเบียน
            </div>

            <div>
                <b class="mb-2 block text-[13px] text-[#0b1220]">สำรวจ</b>
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('courses.index') }}">ค้นหาวิชา</a>
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('plans.index') }}">แผนการเรียน</a>
                @if (request()->routeIs('home'))
                    <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('home') }}#features">ไฮไลต์</a>
                    <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('home') }}#steps">วิธีใช้งาน</a>
                @endif
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('home') }}#reviews">รีวิวล่าสุด</a>
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('home') }}#faq">คำถามที่พบบ่อย</a>
            </div>

            <div>
                <b class="mb-2 block text-[13px] text-[#0b1220]">บัญชี</b>
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('auth.login') }}">เข้าสู่ระบบ</a>
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('auth.register') }}">สมัครสมาชิก</a>
            </div>

            <div>
                <b class="mb-2 block text-[13px] text-[#0b1220]">ช่วยเหลือ</b>
                <a class="block py-0.75 hover:text-[#0b1220] hover:underline" href="{{ route('home') }}#steps">เริ่มต้นใช้งาน</a>
            </div>
        </div>

        <div class="border-t border-[#0c326421] pt-4.5">
            CPRATING &copy; {{ date('Y') }} สงวนลิขสิทธิ์
        </div>
    </div>
</footer>
