<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title.' | ' ?? '' }} {{ config('app.name', 'Valheim Recipes') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
        <!-- The "defer" attribute is important to make sure Alpine waits for Livewire to load first. -->
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <livewire:styles />
        
        @stack('styles')
        
    </head>
    <body class="bg-gray-900 min-h-screen flex flex-col text-gray-100">
        
        @include('nav')

        <main class="container max-w-6xl flex-grow mx-auto my-6 px-4
            lg:my-12
            lg:p-0
        ">
            {{ $slot }}
        </main>
        
        <footer class="w-full bg-gray-800 border-t border-gray-700 py-6">
            
            <div class="max-w-6xl mx-auto text-gray-500 text-sm italic text-center">
                <a class="inline-block underline hover:no-underline" href="https://remixicon.com/" rel="noopener">Remix Icon</a> &middot; <a class="inline-block my-2 underline hover:no-underline" href="https://valheim.fandom.com" rel="noopener">Valheim Wiki Images</a> &middot; <a class="inline-block underline hover:no-underline" rel="noopener nofollow" href="https://github.com/jessinthecloud/valheim">jessinthecloud/valheim</a> &middot; <a class="inline-block underline hover:no-underline" rel="noopener nofollow" href="https://twitter.com/jessinthecloud">@jessinthecloud</a> &middot; <a class="inline-block underline hover:no-underline" rel="noopener nofollow" href="https://jessinthe.cloud">jessinthe.cloud</a>
            </div>
            
        </footer>
        @stack('scripts')
        <livewire:scripts />
    </body>
</html>
