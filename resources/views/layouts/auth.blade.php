<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'เข้าสู่ระบบ') - CPRATING</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen text-slate-800"
>
    <main
        class="auth-scene relative isolate flex min-h-screen items-center justify-center overflow-hidden px-4 py-6 sm:px-6 sm:py-10"
    >
        <div
            class="pointer-events-none absolute inset-0 -z-10"
            aria-hidden="true"
        >
            <i
                data-lucide="book-open"
                class="auth-icon left-[13%] top-[18%]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="smartphone"
                class="auth-icon right-[13%] top-[14%]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="laptop"
                class="auth-icon bottom-[18%] left-[13%]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="building-2"
                class="auth-icon bottom-[17%] right-[13%]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="graduation-cap"
                class="auth-icon right-[25%] top-[36%]"
                aria-hidden="true"
            ></i>
        </div>

        <section
            class="w-full max-w-[440px]"
        >
            <div
                class="mb-6 flex justify-center sm:mb-10"
            >
                <a
                    href="{{ url('/') }}"
                    class="grid size-[100px] place-items-center rounded-full bg-[#d8d8d8] text-base font-normal text-slate-700"
                    aria-label="กลับหน้าหลัก"
                >
                    logo
                </a>
            </div>

            <section
                class="rounded-[19px] border border-[#777] bg-white px-5 py-6 shadow-[0_10px_13px_rgba(0,0,0,0.28)] sm:px-8 sm:py-8"
            >
                @yield('content')
            </section>
        </section>
    </main>
</body>
</html>
