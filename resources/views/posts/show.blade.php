@extends('layouts.app')
@section('title', $post->meta_title ?: $post->title)

@section('content')
@php
    $fallbackImage = 'linear-gradient(135deg, #101828, #0f766e)';
@endphp

<header class="post-hero" style="--post-image: url('{{ $post->image ?: '' }}'), {{ $fallbackImage }};">
    <div class="post-hero-inner">
        <span class="badge">{{ $post->categoryLabel() }}</span>
        <h1>{{ $post->title }}</h1>
        <p style="color:#d0d5dd;max-width:760px;margin:0 auto 1rem;">{{ $post->excerpt }}</p>
        <div class="post-meta" style="justify-content:center;color:#d0d5dd;">
            <span>By {{ $post->user->name }}</span>
            <span>{{ optional($post->published_at)->format('M d, Y') }}</span>
            <span>{{ $post->readingTime() }}</span>
            <span>{{ $post->approvedComments->count() }} comments</span>
        </div>
    </div>
</header>

<main class="container">
    <article class="post-content">
        {!! $post->body !!}
    </article>

    <section class="comments-section">
        <h2 class="section-title">{{ $post->approvedComments->count() }} Comments</h2>

        @forelse($post->approvedComments as $comment)
            <div class="comment">
                <div class="post-meta">
                    <strong>{{ $comment->name }}</strong>
                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p style="margin-bottom:0;">{{ $comment->body }}</p>
            </div>
        @empty
            <p class="muted">No approved comments yet.</p>
        @endforelse

        <div class="panel" style="padding:1.5rem;margin-top:1.25rem;">
            <h3 style="margin-top:0;">Join the discussion</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('post.comment', $post) }}">
                @csrf
                @guest
                    <div class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                @else
                    <p class="muted">Commenting as {{ auth()->user()->name }}.</p>
                @endguest
                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="body" required>{{ old('body') }}</textarea>
                </div>
                <button type="submit" class="btn">Submit for approval</button>
            </form>
        </div>
    </section>

    @if($related->isNotEmpty())
        <section style="margin-top:2rem;">
            <span class="eyebrow">Keep reading</span>
            <h2 class="section-title">Related stories</h2>
            <div class="posts-grid">
                @foreach($related as $r)
                    <article class="post-card">
                        <a href="{{ route('post.show', $r->slug) }}" class="post-card-img" style="--card-image: url('{{ $r->image ?: '' }}'), linear-gradient(135deg, #0f766e, #2563eb);" aria-label="{{ $r->title }}"></a>
                        <div class="post-card-body">
                            <span class="badge">{{ $r->categoryLabel() }}</span>
                            <h3 style="margin-top:.7rem;"><a href="{{ route('post.show', $r->slug) }}">{{ $r->title }}</a></h3>
                            <p class="muted">{{ $r->excerpt }}</p>
                            <a href="{{ route('post.show', $r->slug) }}" class="read-more">Read story</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
