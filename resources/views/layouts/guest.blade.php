<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RS Samsoe Hidajat') }}</title>

        <link rel="icon" href="https://rssamsoehidajat.com/wp-content/uploads/2023/11/cropped-logo-rssh-1-32x32.webp" sizes="32x32">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Custom Pushed Styles -->
        @stack('styles')
    </head>
    <body>
        {{-- <div class="font-sans text-gray-900 antialiased"> --}}
            {{ $slot }}
        {{-- </div> --}}

        @livewireScripts

        <!-- Custom Pushed Scripts -->
        @stack('scripts')
    </body>
</html>
