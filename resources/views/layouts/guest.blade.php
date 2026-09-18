<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Riak Coffee POS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#2B1810] bg-[#F9F6F0] antialiased select-none">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Brand Identity -->
            <div class="flex flex-col items-center gap-2 mb-4 text-center">
                <a href="/" class="flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-2xl bg-[#4A2E1B] border border-[#382214] flex items-center justify-center text-[#FFFDF9] shadow-lg font-serif font-extrabold text-2xl group-hover:bg-[#8C6239] transition duration-200">
                        R
                    </div>
                    <span class="font-serif font-bold text-2xl tracking-widest text-[#2B1810] mt-3">
                        RIAK COFFEE
                    </span>
                    <span class="text-[11px] uppercase tracking-widest text-[#8C6239] font-medium">
                        Coffee &amp; Lake &bull; Point of Sale
                    </span>
                </a>
            </div>

            <!-- Login Container Card -->
            <div class="w-full sm:max-w-md mt-2 px-6 py-8 bg-[#FFFDF9] border border-[#E8DFD8] shadow-xl sm:rounded-2xl">
                {{ $slot }}
            </div>

            <!-- Footer Notes -->
            <div class="mt-6 text-center text-xs text-[#A6978A]">
                &copy; {{ date('Y') }} Riak Coffee. Jalan ke Dasong, Pancasari, Sukasada, Buleleng.
            </div>
        </div>
    </body>
</html>