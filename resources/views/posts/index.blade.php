@extends('layouts.app')
@section('title', 'PulsePress')

@section('content')
@php
    $fallbackImage = 'linear-gradient(135deg, #0f766e, #2563eb)';
@endphp

<section class="hero">
    <div class="hero-inner">
        @if($featured)
            <article class="hero-feature" style="--feature-image: url('{{ $featured->image ?: '' }}'), {{ $fallbackImage }};">
                <span class="badge">{{ $featured->categoryLabel() }}</span>
                <h1>{{ $featured->title }}</h1>
                <p>{{ $featured->excerpt }}</p>
                <div class="post-meta" style="color:#d0d5dd;">
                    <span>By {{ $featured->user->name }}</span>
                    <span>{{ optional($featured->published_at)->format('M d, Y') }}</span>
                    <span>{{ $featured->readingTime() }}</span>
                </div>
                <div style="margin-top:1rem;">
                    <a class="btn" href="{{ route('post.show', $featured->slug) }}">Read feature</a>
                </div>
            </article>
        @else
            <article class="hero-feature" style="--feature-image: {{ $fallbackImage }};">
                <span class="badge">Editorial</span>
                <h1>Publish sharp stories across every niche.</h1>
                <p>Create an account, submit articles for approval, and run a modern multi-author blog from one Laravel dashboard.</p>
                <div style="margin-top:1rem;">
                    <a class="btn" href="{{ route('register') }}">Start writing</a>
                </div>
            </article>
        @endif

        <aside class="hero-side">
            <div class="search-panel">
                <span class="eyebrow">Find your next read</span>
                <h2 style="margin:.35rem 0 0;">Search the archive</h2>
                <form class="search-form" method="GET" action="{{ route('home') }}">
                    <input type="text" name="search" placeholder="Search articles" value="{{ request('search') }}">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>

            <div class="trend-list">
                <span class="eyebrow" style="color:#7dd3fc;">Trending now</span>
                @forelse($trending as $item)
                    <a class="trend-item" href="{{ route('post.show', $item->slug) }}">
                        <strong>{{ $item->title }}</strong><br>
                        <small>{{ $item->categoryLabel() }} / {{ number_format($item->views) }} views</small>
                    </a>
                @empty
                    <p class="muted" style="color:#98a2b3;">Published posts will appear here.</p>
                @endforelse
            </div>
        </aside>
    </div>
</section>

<main class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="categories">
        <a href="{{ route('home') }}" class="cat-badge {{ !request('category') ? 'active' : '' }}">All niches</a>
        @foreach($categories as $key => $label)
            @if(in_array($key, $activeCategories, true))
                <a href="{{ route('home', ['category' => $key]) }}"
                   class="cat-badge {{ request('category') === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endif
        @endforeach
    </div>

    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:end;margin-bottom:1rem;">
        <div>
            <span class="eyebrow">Latest posts</span>
            <h2 class="section-title">
                {{ request('category') ? ($categories[request('category')] ?? ucfirst(request('category'))) : 'Fresh from every niche' }}
            </h2>
        </div>
        @auth
            @if(auth()->user()->isAuthor())
                <a href="{{ route('admin.post.create') }}" class="btn btn-ghost">New post</a>
            @endif
        @endauth
    </div>

    @if($posts->isEmpty())
        <div class="panel" style="padding:3rem;text-align:center;">
            <h3>No posts found</h3>
            <p class="muted">Try a different search or category filter.</p>
        </div>
    @else
        <div class="posts-grid">
            @foreach($posts as $post)
                <article class="post-card">
                    <a href="{{ route('post.show', $post->slug) }}" class="post-card-img" style="--card-image: url('{{ $post->image ?: '' }}'), {{ $fallbackImage }};" aria-label="{{ $post->title }}"></a>
                    <div class="post-card-body">
                        <div class="post-meta">
                            <span class="badge">{{ $post->categoryLabel() }}</span>
                            <span>{{ optional($post->published_at)->diffForHumans() }}</span>
                            <span>{{ $post->readingTime() }}</span>
                        </div>
                        <h3><a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a></h3>
                        <p class="muted">{{ $post->excerpt }}</p>
                        <a href="{{ route('post.show', $post->slug) }}" class="read-more">Read more</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $posts->withQueryString()->links() }}
        </div>
    @endif
</main>
@endsection
