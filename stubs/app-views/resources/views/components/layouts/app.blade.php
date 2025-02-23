<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        @stack('meta')

        <!-- Font Awesome Icons CDN -->
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Favicon & Apple Touch Icon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">

        <!-- Vite Styles & Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/sass/bulma.scss',
            'resources/js/app.js'
        ])

        {{-- Additional Styles and Scripts - Stacked Assets for use in layout with
            @push('assets') ... @endpush
                or
            @pushonce('assets') ... @endpushonce

            directives.

            You can push assets to the stack using the @push or @pushonce directives.

            Example:
            @push('assets')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            @endpush

            Then in the layout, you can use the stacked assets like this:

            @stack('assets')

            This will output the script tag for chart.js.
            You can also use @pushonce to ensure that the assets are only pushed once, even if the stack is pushed multiple times.

            @pushonce('assets')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            @endpushonce

            This will only output the script tag for chart.js once, even if the stack is pushed multiple times.
            Note that @push and @pushonce can be nested inside each other, so you can push multiple assets to the same stack.
            You can also use @stack to output all the assets in a stack, or @stack('stack_name') to output only the assets in a specific stack.
            @stack('assets') will output all the assets in the 'assets' stack, while @stack('stack_name') will output only the assets in the'stack_name' stack.
            You can use @stack('assets') in the layout to output all the assets that were pushed to the stack, or you can use @stack('stack_name') in the layout to output only the assets in a specific stack.
            Note that @stack can be nested inside each other, so you can output multiple stacks in the same layout.
         --}}
        @stack('assets')
    </head>
    <body class="min-h-screen sans-serif">

    @yield('before-content')

    <main class="main">
        <div class="container py-8">
            @if(isset($slot) && $slot->isNotEmpty())
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>
    </main>

    @yield('after-content')

    <footer class="footer">
        <div class="container">
            <div class="content has-text-centered">
                <p>
                    <strong>Bulma</strong> by <a href="https://jgthms.com" target="_blank">jgthms.com</a>. The source code is licensed
                    <a href="https://opensource.org/licenses/mit-license.php" target="_blank">MIT</a>
                </p>
                <p>
                    <a class="icon" href="https://github.com/modularavel/modularavel" target="_blank">
                        <i class="fab fa-github"></i>
                    </a>
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
    </body>
</html>
