<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $postQuery = $this->visiblePostsQuery();

        $stats = [
            'posts' => (clone $postQuery)->count(),
            'published' => (clone $postQuery)->where('status', Post::STATUS_PUBLISHED)->count(),
            'pending_posts' => $user->isAdmin() ? Post::pending()->count() : (clone $postQuery)->where('status', Post::STATUS_PENDING)->count(),
            'comments' => $user->isAdmin() ? Comment::count() : Comment::whereHas('post', fn ($q) => $q->where('user_id', $user->id))->count(),
            'pending_comments' => $user->isAdmin() ? Comment::where('approved', false)->count() : Comment::where('approved', false)->whereHas('post', fn ($q) => $q->where('user_id', $user->id))->count(),
            'users' => $user->isAdmin() ? User::count() : 0,
        ];

        $recentPosts = (clone $postQuery)->with('user')->latest()->limit(6)->get();
        $recentComments = Comment::with('post')
            ->when(!$user->isAdmin(), fn ($q) => $q->whereHas('post', fn ($posts) => $posts->where('user_id', $user->id)))
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts', 'recentComments'));
    }

    public function posts(Request $request)
    {
        $posts = $this->visiblePostsQuery()
            ->with(['user', 'comments'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(12);

        return view('admin.posts', compact('posts'));
    }

    public function createPost()
    {
        abort_unless(Auth::user()->isAuthor(), 403);

        return view('admin.post-form', ['post' => null]);
    }

    public function storePost(Request $request)
    {
        abort_unless(Auth::user()->isAuthor(), 403);

        $data = $this->validatedPost($request);
        $status = $this->requestedStatus($request);

        Post::create([
            'user_id'      => Auth::id(),
            'title'        => $data['title'],
            'excerpt'      => $data['excerpt'] ?? null,
            'body'         => $data['body'],
            'image'        => $data['image'] ?? null,
            'category'     => $data['category'],
            'status'       => $status,
            'published'    => $status === Post::STATUS_PUBLISHED,
            'published_at' => $status === Post::STATUS_PUBLISHED ? now() : null,
            'submitted_at' => $status === Post::STATUS_PENDING ? now() : null,
            'approved_at'  => in_array($status, [Post::STATUS_APPROVED, Post::STATUS_PUBLISHED], true) ? now() : null,
            'approved_by'  => Auth::user()->isAdmin() && in_array($status, [Post::STATUS_APPROVED, Post::STATUS_PUBLISHED], true) ? Auth::id() : null,
            'meta_title'   => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return redirect()->route('admin.posts')->with('success', 'Post created successfully.');
    }

    public function editPost(Post $post)
    {
        $this->authorizePostAccess($post);

        return view('admin.post-form', compact('post'));
    }

    public function updatePost(Request $request, Post $post)
    {
        $this->authorizePostAccess($post);

        $data = $this->validatedPost($request);
        $status = $this->requestedStatus($request, $post);

        $post->update([
            'title'        => $data['title'],
            'excerpt'      => $data['excerpt'] ?? null,
            'body'         => $data['body'],
            'image'        => $data['image'] ?? null,
            'category'     => $data['category'],
            'status'       => $status,
            'published'    => $status === Post::STATUS_PUBLISHED,
            'published_at' => $status === Post::STATUS_PUBLISHED && !$post->published_at ? now() : ($status === Post::STATUS_PUBLISHED ? $post->published_at : null),
            'submitted_at' => $status === Post::STATUS_PENDING && !$post->submitted_at ? now() : $post->submitted_at,
            'approved_at'  => Auth::user()->isAdmin() && in_array($status, [Post::STATUS_APPROVED, Post::STATUS_PUBLISHED], true) ? now() : $post->approved_at,
            'approved_by'  => Auth::user()->isAdmin() && in_array($status, [Post::STATUS_APPROVED, Post::STATUS_PUBLISHED], true) ? Auth::id() : $post->approved_by,
            'rejection_reason' => null,
            'meta_title'   => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        return redirect()->route('admin.posts')->with('success', 'Post updated successfully.');
    }

    public function deletePost(Post $post)
    {
        $this->authorizePostAccess($post);

        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    public function approvePost(Post $post)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $post->update([
            'status' => Post::STATUS_PUBLISHED,
            'published' => true,
            'published_at' => $post->published_at ?? now(),
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Post approved and published.');
    }

    public function rejectPost(Request $request, Post $post)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $post->update([
            'status' => Post::STATUS_REJECTED,
            'published' => false,
            'published_at' => null,
            'rejection_reason' => $data['rejection_reason'] ?? 'Needs revision before publishing.',
        ]);

        return back()->with('success', 'Post sent back for revision.');
    }

    public function approveComment(Comment $comment)
    {
        $this->authorizeCommentAccess($comment);

        $comment->update(['approved' => true]);

        return back()->with('success', 'Comment approved.');
    }

    public function deleteComment(Comment $comment)
    {
        $this->authorizeCommentAccess($comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }

    public function comments()
    {
        $comments = Comment::with('post')
            ->when(!Auth::user()->isAdmin(), fn ($q) => $q->whereHas('post', fn ($posts) => $posts->where('user_id', Auth::id())))
            ->latest()
            ->paginate(20);

        return view('admin.comments', compact('comments'));
    }

    public function users()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $users = User::withCount('posts')->latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        abort_if($user->id === Auth::id(), 422, 'You cannot change your own role.');

        $data = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_AUTHOR, User::ROLE_USER])],
        ]);

        $user->update(['role' => $data['role']]);

        return back()->with('success', 'User role updated.');
    }

    private function validatedPost(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category' => ['required', Rule::in(array_keys(Post::CATEGORIES))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'url', 'max:2048'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status_action' => ['nullable', 'in:draft,submit,publish'],
        ]);
    }

    private function requestedStatus(Request $request, ?Post $post = null): string
    {
        $action = $request->input('status_action', 'draft');

        if (Auth::user()->isAdmin() && $action === 'publish') {
            return Post::STATUS_PUBLISHED;
        }

        if ($action === 'submit') {
            return Post::STATUS_PENDING;
        }

        return $post?->status === Post::STATUS_PUBLISHED && Auth::user()->isAdmin()
            ? Post::STATUS_PUBLISHED
            : Post::STATUS_DRAFT;
    }

    private function visiblePostsQuery()
    {
        $query = Post::query();

        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    private function authorizePostAccess(Post $post): void
    {
        abort_unless(Auth::user()->isAdmin() || $post->user_id === Auth::id(), 403);
    }

    private function authorizeCommentAccess(Comment $comment): void
    {
        abort_unless(
            Auth::user()->isAdmin() || $comment->post()->where('user_id', Auth::id())->exists(),
            403
        );
    }
}
