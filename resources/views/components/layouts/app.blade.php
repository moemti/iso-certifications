<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Certifications') }}</title>
    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body class="portal-body">
    <div class="page">

        <section class="page-header" role="banner">
            <div class="header-inner">
                <a class="brand" href="{{ url('/') }}" aria-label="Home">
                    <div class="brand-mark">C</div>
                    <div class="brand-text">
                        <strong>Certifications</strong>
                        <span>{{ request()->getHost() }}</span>
                    </div>
                </a>
                <nav class="header-nav" aria-label="Site navigation">
                    @auth
                        <div class="user-info">
                            @if(auth()->user()->company ?? null)
                                <span class="company-name">{{ auth()->user()->company->legal_name }}</span>
                                <span class="user-sep">/</span>
                            @endif
                            <span class="user-name">{{ auth()->user()->name }}</span>
                        </div>
                        @if (Route::has('dashboard'))
                            <a class="header-link" href="{{ url('/dashboard') }}">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" style="display:inline">
                            @csrf
                            <button type="submit" class="header-link header-btn">Sign out</button>
                        </form>
                    @else
                        @if (Route::has('login'))
                            <a class="header-link header-link--primary" href="{{ route('login') }}">Sign in</a>
                        @endif
                        @if (Route::has('register'))
                            <a class="header-link" href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </section>


        <main class="main-content">
            {{ $slot }}
        </main>

        <footer class="page-footer">
            &copy; {{ date('Y') }} Certifications. All rights reserved.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
