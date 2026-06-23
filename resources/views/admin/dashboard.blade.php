@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="admin-layout">
    @include('admin.partials.sidebar')

    <main class="admin-content">
        <div class="admin-header">
            <div>
                <span class="eyebrow">{{ auth()->user()->role }} workspace</span>
                <h1 style="margin:.2rem 0 0;">Dashboard</h1>
                <p class="muted" style="margin:.2rem 0 0;">Track editorial activity, approvals, comments, and publishing status.</p>
            </div>
            @if(auth()->user()->isAuthor())
                <a href="{{ route('admin.post.create') }}" class="btn">Write post</a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="stats-grid">
            <div class="stat-card panel">
                <div class="stat-number">{{ $stats['posts'] }}</div>
                <div class="stat-label">Total posts</div>
            </div>
            <div class="stat-card panel">
                <div class="stat-number">{{ $stats['published'] }}</div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-card panel">
                <div class="stat-number">{{ $stats['pending_posts'] }}</div>
                <div class="stat-label">Pending posts</div>
            </div>
            <div class="stat-card panel">
                <div class="stat-number">{{ $stats['pending_comments'] }}</div>
                <div class="stat-label">Pending comments</div>
            </div>
        </section>

        <section class="grid two-col">
            <div class="panel" style="padding:1rem;">
                <h2 class="section-title">Recent posts</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPosts as $post)
                                <tr>
                                    <td><a class="read-more" href="{{ route('admin.post.edit', $post) }}">{{ Str::limit($post->title, 42) }}</a></td>
                                    <td>{{ $post->user->name }}</td>
                                    <td><span class="badge {{ $post->status }}">{{ $post->statusLabel() }}</span></td>
                                    <td class="muted">{{ $post->created_at->format('M d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="muted">No posts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel" style="padding:1rem;">
                <h2 class="section-title">Recent comments</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Post</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentComments as $comment)
                                <tr>
                                    <td>{{ $comment->name }}</td>
                                    <td class="muted">{{ Str::limit($comment->post->title, 28) }}</td>
                                    <td>
                                        @if($comment->approved)
                                            <span class="badge published">Approved</span>
                                        @else
                                            <span class="badge pending">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="muted">No comments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection
