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
    <body class="min-h-screen overflow-x-hidden font-sans antialiased @yield('body_class')">
        @yield('content')
    </body>
</html>
