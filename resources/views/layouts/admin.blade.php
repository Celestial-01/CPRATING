<!DOCTYPE html>
<html lang="th" class="[scrollbar-gutter:stable]">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Admin') - CPRATING</title>
        @vite(['resources/css/app.css', 'resources/js/admin-app.js'])
    </head>

<body class="flex min-h-screen flex-col bg-white font-sans text-slate-900 antialiased">
    <x-admin.navbar />

    <div class="flex-1">
        @yield('content')
    </div>

    <x-admin.footer />
</body>
