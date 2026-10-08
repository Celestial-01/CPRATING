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
                class="absolute left-[13%] top-[18%] h-12 w-12 stroke-[#9dc6e7] [stroke-linecap:round] [stroke-linejoin:round] [stroke-width:2.7]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="smartphone"
                class="absolute right-[13%] top-[14%] h-12 w-12 stroke-[#9dc6e7] [stroke-linecap:round] [stroke-linejoin:round] [stroke-width:2.7]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="laptop"
                class="absolute bottom-[18%] left-[13%] h-12 w-12 stroke-[#9dc6e7] [stroke-linecap:round] [stroke-linejoin:round] [stroke-width:2.7]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="building-2"
                class="absolute bottom-[17%] right-[13%] h-12 w-12 stroke-[#9dc6e7] [stroke-linecap:round] [stroke-linejoin:round] [stroke-width:2.7]"
                aria-hidden="true"
            ></i>
            <i
                data-lucide="graduation-cap"
                class="absolute right-[25%] top-[36%] h-12 w-12 stroke-[#9dc6e7] [stroke-linecap:round] [stroke-linejoin:round] [stroke-width:2.7]"
                aria-hidden="true"
            ></i>
        </div>

        <section
            class="w-full max-w-[440px]"
        >
            @hasSection('showBrand')
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
            @endif

            <section
                class="rounded-[19px] border border-[#777] bg-white px-6 py-6 shadow-[0_10px_13px_rgba(0,0,0,0.28)] sm:px-8 sm:py-8"
            >
                @yield('content')
            </section>
        </section>
    </main>
</body>
</html>
