<!DOCTYPE html>
<html lang="th" class="[scrollbar-gutter:stable]">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Admin') - CPRATING</title>
        @vite(['resources/css/app.css', 'resources/js/admin-app.js'])
    </head>
    <body class="min-h-screen bg-white font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col">
            <x-admin.navbar />

            <div class="flex-1">
                @yield('content')
            </div>

            <x-admin.footer />
        </div>
    </body>
</html>
