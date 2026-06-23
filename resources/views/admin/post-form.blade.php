@extends('layouts.app')
@section('title', $post ? 'Edit Post' : 'New Post')

@section('content')
@php
    $currentAction = old('status_action');

    if (!$currentAction && $post) {
        $currentAction = $post->status === \App\Models\Post::STATUS_PUBLISHED ? 'publish' : ($post->status === \App\Models\Post::STATUS_PENDING ? 'submit' : 'draft');
    }

    $currentAction = $currentAction ?: 'draft';

    if (!auth()->user()->isAdmin() && $currentAction === 'publish') {
        $currentAction = 'submit';
    }
@endphp
<div class="admin-layout">
    @include('admin.partials.sidebar')

    <main class="admin-content">
        <div class="admin-header">
            <div>
                <span class="eyebrow">{{ $post ? 'Edit story' : 'Create story' }}</span>
                <h1 style="margin:.2rem 0 0;">{{ $post ? $post->title : 'New post' }}</h1>
                @if($post)
                    <p class="muted" style="margin:.2rem 0 0;">Current status: <span class="badge {{ $post->status }}">{{ $post->statusLabel() }}</span></p>
                @endif
            </div>
            <a href="{{ route('admin.posts') }}" class="btn btn-ghost">Back to posts</a>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($post?->rejection_reason)
            <div class="alert alert-error">{{ $post->rejection_reason }}</div>
        @endif

        <form method="POST" action="{{ $post ? route('admin.post.update', $post) : route('admin.post.store') }}" class="panel" style="padding:1.25rem;">
            @csrf
            @if($post) @method('PUT') @endif

            <div class="grid two-col">
                <section>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required placeholder="A clear, specific headline">
                    </div>

                    <div class="form-group">
                        <label>Excerpt</label>
                        <textarea name="excerpt" style="min-height:90px;" placeholder="A short summary for cards and search results">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Body</label>
                        <textarea name="body" style="min-height:420px;font-family:ui-monospace, SFMono-Regular, Menlo, monospace;font-size:.92rem;" required placeholder="<p>Write your article with clean HTML.</p>">{{ old('body', $post->body ?? '') }}</textarea>
                    </div>
                </section>

                <aside>
                    <div class="form-group">
                        <label>Niche</label>
                        <select name="category" required>
                            @foreach(\App\Models\Post::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $post->category ?? 'general') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cover image URL</label>
                        <input type="url" name="image" value="{{ old('image', $post->image ?? '') }}" placeholder="https://example.com/image.jpg">
                    </div>

                    <div class="form-group">
                        <label>SEO title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>SEO description</label>
                        <textarea name="meta_description" style="min-height:90px;">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    </div>

                    <div class="panel" style="padding:1rem;box-shadow:none;background:#f9fafb;">
                        <h3 style="margin-top:0;">Publishing action</h3>
                        <label style="display:flex;gap:.6rem;align-items:flex-start;font-weight:650;">
                            <input type="radio" name="status_action" value="draft" style="width:18px;margin-top:.25rem;" {{ $currentAction === 'draft' ? 'checked' : '' }}>
                            <span>Save as draft<br><small class="muted">Keep it private while editing.</small></span>
                        </label>
                        <label style="display:flex;gap:.6rem;align-items:flex-start;font-weight:650;margin-top:.8rem;">
                            <input type="radio" name="status_action" value="submit" style="width:18px;margin-top:.25rem;" {{ $currentAction === 'submit' ? 'checked' : '' }}>
                            <span>Submit for approval<br><small class="muted">Admins can review and publish it.</small></span>
                        </label>
                        @if(auth()->user()->isAdmin())
                            <label style="display:flex;gap:.6rem;align-items:flex-start;font-weight:650;margin-top:.8rem;">
                                <input type="radio" name="status_action" value="publish" style="width:18px;margin-top:.25rem;" {{ $currentAction === 'publish' ? 'checked' : '' }}>
                                <span>Publish now<br><small class="muted">Make this post public immediately.</small></span>
                            </label>
                        @endif
                    </div>

                    <div class="actions" style="margin-top:1rem;">
                        <button type="submit" class="btn">{{ $post ? 'Save changes' : 'Create post' }}</button>
                        @if($post && $post->published)
                            <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="btn btn-secondary">View public</a>
                        @endif
                    </div>
                </aside>
            </div>
        </form>
    </main>
</div>
@endsection
