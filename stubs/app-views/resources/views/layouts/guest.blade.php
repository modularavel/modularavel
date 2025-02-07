<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        @stack('meta')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <!-- Scripts -->
        @vite([
            'resources/sass/bootstrap.scss',
            'resources/css/app.css',
            'resources/js/app.js'
        ])
    </head>
    <body class="font-sans antialiased">
    <div id="topbar" class="container-fluid bg-pattern-black py-2 sticky-top">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-6 d-none d-sm-block">
                <a href="mailto:" class="btn btn-link btn-sm text-white text-decoration-none">
                    <i class="fa fa-envelope"></i> support@laradate.com
                </a>
            </div>
            <div class="col-12 col-sm-6">
                <div id="social-links" class="gap-3 text-end hstack justify-content-center justify-content-sm-end">
                    <a href="#!" target="_blank" class="social btn btn-light btn-sm rounded-circle shadow-sm">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a href="#!" target="_blank" class="social btn btn-light btn-sm rounded-circle shadow-sm">
                        <i class="fa fa-instagram"></i>
                    </a>
                    <a href="#!" target="_blank" class="social btn btn-light btn-sm rounded-circle shadow-sm">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <div class="vr text-white mx-1"></div>
                    <a href="#!" class="btn btn-outline-light btn-sm rounded-pill">Language</a>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="sticky-top bg-pattern">
<nav class="navbar navbar-expand-md navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-2" href="https://laradate.test">
                <i class="fa fa-heart text-secondary"></i> Laravel
            </a>
            <button class="navbar-toggler bg-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                </ul>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-2">
    <a class="nav-link  " href="https://laradate.test">Home</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link " href="#!">About</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link " href="#!">Terms</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link " href="#!">Privacy</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link " href="#!">Blog</a>
</li>
                    <li class="nav-item mx-2 mx-lg-3">
        <a class="nav-link" href="https://laradate.test/register">Register</a>
    </li>
    <li class="nav-item">
        <a class="btn btn-secondary rounded-pill" href="https://laradate.test/login">Login</a>
    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
       <main>
       {{ $slot }}
       </main>
       <footer class="py-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item mx-2">
    <a class="nav-link   px-2 text-muted " href="https://laradate.test">Home</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link  px-2 text-muted " href="#!">About</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link  px-2 text-muted " href="#!">Terms</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link  px-2 text-muted " href="#!">Privacy</a>
</li>
<li class="nav-item mx-2">
    <a class="nav-link  px-2 text-muted " href="#!">Blog</a>
</li>
    </ul>
    <p class="text-center text-muted mb-0">© 2024 Laravel - All Right Reserved</p>
</footer>
    </body>
</html>
