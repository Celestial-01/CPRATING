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
        @if (request()->routeIs('auth.login', 'auth.register')) data-auth-entry @endif
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
                        class="inline-flex items-center justify-center rounded-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-sky-200"
                        aria-label="กลับหน้าหลัก"
                    >
                        <img
                            src="{{ asset('images/cprating-logo.svg') }}"
                            alt=""
                            class="block h-auto w-[210px] sm:w-[230px]"
                            width="307"
                            height="80"
                        >
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

    <x-auth.legal-modals />
</body>
</html>
