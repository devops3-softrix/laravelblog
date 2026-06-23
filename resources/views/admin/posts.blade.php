@extends('layouts.app')
@section('title', 'Manage Posts')

@section('content')
<div class="admin-layout">
    @include('admin.partials.sidebar')

    <main class="admin-content">
        <div class="admin-header">
            <div>
                <span class="eyebrow">Editorial queue</span>
                <h1 style="margin:.2rem 0 0;">Posts</h1>
            </div>
            @if(auth()->user()->isAuthor())
                <a href="{{ route('admin.post.create') }}" class="btn">New post</a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="categories" style="margin-bottom:1rem;">
            <a class="cat-badge {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.posts') }}">All</a>
            @foreach(['draft', 'pending', 'published', 'rejected'] as $status)
                <a class="cat-badge {{ request('status') === $status ? 'active' : '' }}" href="{{ route('admin.posts', ['status' => $status]) }}">{{ ucfirst($status) }}</a>
            @endforeach
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Niche</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Engagement</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <strong>{{ Str::limit($post->title, 46) }}</strong>
                                @if($post->rejection_reason)
                                    <div class="muted" style="font-size:.82rem;">{{ Str::limit($post->rejection_reason, 70) }}</div>
                                @endif
                            </td>
                            <td><span class="badge">{{ $post->categoryLabel() }}</span></td>
                            <td>{{ $post->user->name }}</td>
                            <td><span class="badge {{ $post->status }}">{{ $post->statusLabel() }}</span></td>
                            <td class="muted">{{ $post->comments->count() }} comments / {{ number_format($post->views) }} views</td>
                            <td class="muted">{{ $post->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.post.edit', $post) }}" class="btn btn-sm btn-ghost">Edit</a>
                                    @if($post->published)
                                        <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                    @endif
                                    @if(auth()->user()->isAdmin() && $post->status === 'pending')
                                        <form method="POST" action="{{ route('admin.post.approve', $post) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.post.reject', $post) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="rejection_reason" value="Please revise and resubmit.">
                                            <button type="submit" class="btn btn-sm btn-warning">Reject</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.post.delete', $post) }}" onsubmit="return confirm('Delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="muted" style="text-align:center;padding:2rem;">No posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $posts->withQueryString()->links() }}
        </div>
    </main>
</div>
@endsection
