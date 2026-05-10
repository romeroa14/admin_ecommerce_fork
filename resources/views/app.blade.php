<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Domain Verification -->
    <meta name="facebook-domain-verification" content="djxnj6mu0dzbti6dzs5xrib0wehyub" />

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-MH9JNBR34D"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-MH9JNBR34D', { send_page_view: false });
    </script>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.ts', "resources/css/app.css"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>