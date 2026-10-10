@props(['title', 'description' => null])

<main class="mx-auto w-full max-w-[1120px] px-6 pb-10">
    <header class="pt-8">
        <h1 class="text-2xl font-semibold leading-8 text-slate-900 sm:text-3xl">
            {{ $title }}
        </h1>
        <p class="mt-2 min-h-5 text-sm text-slate-500">
            {{ $description }}
        </p>
    </header>

    @if (session('success'))
        <div class="mt-6 rounded-[15px] border border-[#bfddfa] bg-[#edf7ff] px-4 py-3 text-sm text-[#527ba8]">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6">
        {{ $slot }}
    </div>
</main>