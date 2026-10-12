<footer class="bg-[#edf2f9] pt-9 pb-10 text-[13px] text-[#657083]">
    <div class="mx-auto w-full max-w-[1160px] px-6">
        <p class="max-w-[760px] border-b border-[#0c326421] pb-5 leading-[1.6]">
            {{ $disclaimer }}
        </p>

        <div class="grid grid-cols-[1.4fr_repeat(3,1fr)] gap-8 py-7 max-[980px]:grid-cols-2">
            <div>
                <b class="{{ $columnHeadingClass }}">CPRATING</b>
                รีวิวรายวิชาก่อนลงทะเบียน
            </div>

            <div>
                <b class="{{ $columnHeadingClass }}">สำรวจ</b>
                @foreach ($exploreLinks as $link)
                    <a class="{{ $columnLinkClass }}" href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                @endforeach
            </div>

            <div>
                <b class="{{ $columnHeadingClass }}">บัญชี</b>
                <a class="{{ $columnLinkClass }}" href="{{ route('auth.login') }}">เข้าสู่ระบบ</a>
                <a class="{{ $columnLinkClass }}" href="{{ route('auth.register') }}">สมัครสมาชิก</a>
            </div>

            <div>
                <b class="{{ $columnHeadingClass }}">ช่วยเหลือ</b>
                <a class="{{ $columnLinkClass }}" href="{{ route('home').'#steps' }}">เริ่มต้นใช้งาน</a>
            </div>
        </div>

        <div class="border-t border-[#0c326421] pt-4.5">
            CPRATING &copy; {{ date('Y') }} สงวนลิขสิทธิ์
        </div>
    </div>
</footer>
