@extends('layouts.app')
@section('title', 'Manage Comments')

@section('content')
<div class="admin-layout">
    @include('admin.partials.sidebar')

    <main class="admin-content">
        <div class="admin-header">
            <div>
                <span class="eyebrow">Moderation</span>
                <h1 style="margin:.2rem 0 0;">Comments</h1>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Reader</th>
                        <th>Comment</th>
                        <th>Post</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr>
                            <td>
                                <strong>{{ $comment->name }}</strong><br>
                                <span class="muted">{{ $comment->email }}</span>
                            </td>
                            <td>{{ Str::limit($comment->body, 110) }}</td>
                            <td>
                                <a href="{{ $comment->post->published ? route('post.show', $comment->post->slug) : route('admin.post.edit', $comment->post) }}" class="read-more">
                                    {{ Str::limit($comment->post->title, 34) }}
                                </a>
                            </td>
                            <td>
                                @if($comment->approved)
                                    <span class="badge published">Approved</span>
                                @else
                                    <span class="badge pending">Pending</span>
                                @endif
                            </td>
                            <td class="muted">{{ $comment->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    @if(!$comment->approved)
                                        <form method="POST" action="{{ route('admin.comment.approve', $comment) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm">Approve</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.comment.delete', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted" style="text-align:center;padding:2rem;">No comments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $comments->links() }}
        </div>
    </main>
</div>
@endsection
