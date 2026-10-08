<!DOCTYPE html>
<html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >
        <title>@yield('title', 'หน้าหลัก') - CPRATING</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen overflow-x-hidden bg-[#f6f6f6] font-sans text-[#111827] antialiased">
        @yield('content')
    </body>
</html>
