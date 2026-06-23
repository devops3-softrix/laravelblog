<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PulsePress')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="topbar" x-data="siteNavigation" @keydown.escape.window="close()">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark">P</span>
            <span>PulsePress</span>
        </a>
        <button type="button" class="nav-toggle" @click="toggle()" :aria-expanded="open.toString()" aria-controls="primary-navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-links" id="primary-navigation" :class="{ 'is-open': open }" @click.outside="close()">
            <a href="{{ route('home') }}" class="nav-link" @click="close()">Explore</a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="nav-link" @click="close()">Dashboard</a>
                @if(auth()->user()->isAuthor())
                    <a href="{{ route('admin.post.create') }}" class="nav-cta" @click="close()">Write</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-button">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link" @click="close()">Login</a>
                <a href="{{ route('register') }}" class="nav-cta" @click="close()">Sign up</a>
            @endauth
        </div>
    </div>
</nav>

@yield('content')

<footer>
    <p>PulsePress is powered by Laravel, PHP, Blade, Eloquent, and role-based editorial workflows.</p>
</footer>
</body>
</html>
