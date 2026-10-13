@extends('layouts.app')

@section('title', 'บัญชีของฉัน')
@section('body_class', 'bg-[radial-gradient(ellipse_at_0%_20%,#e4f1ff_0%,#f8fbff_40%,#ffffff_85%)]')

@section('content')
    <x-navbar active="profile" />

    <main class="min-h-[calc(100vh-96px)] px-4 pb-16 pt-8 sm:px-6 sm:pt-10" data-profile-page>
        <div class="mx-auto max-w-[1120px]">
            <header class="mb-8 sm:mb-9">
                <h1 class="text-[36px] font-semibold leading-tight tracking-[-0.03em] text-[#0b1220] sm:text-[52px]">
                    บัญชีของฉัน
                </h1>
                <p class="mt-3 max-w-[520px] text-base leading-7 text-slate-500 sm:text-lg">
                    ดูข้อมูลบัญชีและข้อมูลนักศึกษาของคุณ
                </p>
            </header>

            <div class="grid items-start gap-6 lg:grid-cols-[292px_minmax(0,1fr)] lg:gap-7">
                <aside class="rounded-[24px] border border-[#dbe4ef] bg-white p-3 shadow-[0_18px_40px_rgba(20,95,190,0.08)]" aria-label="เมนูบัญชี">
                    <a
                        href="{{ route('profile.show') }}"
                        class="flex items-center gap-3 rounded-[18px] bg-[#dceeff] px-4 py-3.5 text-[#0a7ae6]"
                        aria-current="page"
                    >
                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-[#1684df] text-white shadow-[0_8px_18px_rgba(22,132,223,0.24)]">
                            <i data-lucide="user-round" class="size-5" aria-hidden="true"></i>
                        </span>
                        <span>
                            <strong class="block text-sm font-semibold">โปรไฟล์</strong>
                            <span class="block text-xs text-[#5f82a7]">ข้อมูลบัญชีของคุณ</span>
                        </span>
                    </a>

                    <div class="mt-1 flex items-center gap-3 rounded-[18px] px-4 py-3.5 text-slate-600">
                        <span class="grid size-11 shrink-0 place-items-center rounded-full bg-[#eef3f8] text-slate-500">
                            <i data-lucide="shield-check" class="size-5" aria-hidden="true"></i>
                        </span>
                        <span>
                            <strong class="block text-sm font-semibold">สถานะบัญชี</strong>
                            <span class="block text-xs text-slate-400">
                                {{ $user->googleAccount ? 'เชื่อมต่อกับ Google' : ($user->email_verified_at ? 'ยืนยันอีเมลแล้ว' : 'ยังไม่ยืนยันอีเมล') }}
                            </span>
                        </span>
                    </div>
                </aside>

                <div class="grid min-w-0 gap-6">
                    <section class="rounded-[28px] border border-[#dbe4ef] bg-white px-5 py-7 shadow-[0_18px_40px_rgba(20,95,190,0.08)] sm:px-8 sm:py-8" aria-labelledby="profile-information-title">
                        <h2 id="profile-information-title" class="text-[22px] font-semibold text-[#0b1220] sm:text-2xl">
                            ข้อมูลโปรไฟล์
                        </h2>

                        <div class="mt-7 grid gap-8 md:grid-cols-[156px_minmax(0,1fr)] md:items-center md:gap-8">
                            <div class="text-center">
                                <span class="relative mx-auto grid size-32 place-items-center overflow-hidden rounded-full border-4 border-white shadow-[0_0_0_1px_#dbe4ef,0_12px_28px_rgba(20,95,190,0.12)] sm:size-36 {{ $user->avatarColorClasses() }}">
                                    <span class="text-4xl font-semibold" aria-hidden="true">{{ $user->avatarInitial() }}</span>
                                    @if ($user->avatarUrl())
                                        <img
                                            src="{{ $user->avatarUrl() }}"
                                            alt="รูปโปรไฟล์ของ {{ $user->displayName() }}"
                                            class="absolute inset-0 size-full object-cover"
                                            referrerpolicy="no-referrer"
                                            data-avatar-image
                                        >
                                    @endif
                                </span>
                                <p class="mt-3 text-xs text-slate-500">
                                    {{ $user->avatarUrl() ? 'รูปจากบัญชี Google' : 'รูปเริ่มต้นของบัญชี' }}
                                </p>
                            </div>

                            <dl class="divide-y divide-slate-200">
                                <div class="pb-4">
                                    <dt class="text-xs text-slate-500">ชื่อ</dt>
                                    <dd class="mt-1 text-xl font-semibold text-[#0b1220]">{{ $user->displayName() }}</dd>
                                </div>
                                <div class="pt-4">
                                    <dt class="text-xs text-slate-500">อีเมล</dt>
                                    <dd class="mt-1 break-all text-base font-semibold text-[#0b1220] sm:text-lg">{{ $user->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-8 flex items-start gap-2.5 rounded-[16px] bg-[#f3f8fe] px-4 py-3.5 text-sm leading-6 text-[#527ba8]">
                            <i data-lucide="lock-keyhole" class="mt-1 size-4 shrink-0 text-[#0a7ae6]" aria-hidden="true"></i>
                            <span>
                                {{ $user->googleAccount
                                    ? 'ชื่อ รูปโปรไฟล์ และอีเมลเชื่อมต่อกับบัญชี Google จึงแก้ไขในหน้านี้ไม่ได้'
                                    : 'ชื่อที่คุณตั้งไว้และอีเมลที่ใช้สมัครสมาชิกแสดงอยู่ที่นี่' }}
                            </span>
                        </div>
                    </section>

                    @if ($user->hasCompletedProfile())
                        <section class="rounded-[28px] border border-[#dbe4ef] bg-white px-5 py-7 shadow-[0_18px_40px_rgba(20,95,190,0.08)] sm:px-8 sm:py-8" aria-labelledby="student-information-title">
                            <h2 id="student-information-title" class="text-[22px] font-semibold text-[#0b1220] sm:text-2xl">ข้อมูลนักศึกษา</h2>
                            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[18px] bg-[#f3f8fe] px-4 py-4">
                                    <dt class="text-xs text-slate-500">รหัสนักศึกษา</dt>
                                    <dd class="mt-1 text-base font-semibold tracking-[0.05em] text-[#0b1220]">{{ $user->student_id }}</dd>
                                </div>
                                <div class="rounded-[18px] bg-[#f3f8fe] px-4 py-4">
                                    <dt class="text-xs text-slate-500">สาขา</dt>
                                    <dd class="mt-1 text-base font-semibold text-[#0b1220]">{{ $majorOptions[$user->major_code] ?? $user->major_code }}</dd>
                                </div>
                            </dl>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection
