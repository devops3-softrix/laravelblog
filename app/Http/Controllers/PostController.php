<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()->with('user');

        if ($request->category) {
            $query->byCategory($request->category);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('body', 'like', '%' . $request->search . '%');
            });
        }

        $featured = Post::published()->with('user')->first();
        $posts = $query->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))->paginate(9);
        $categories = Post::CATEGORIES;
        $activeCategories = Post::published()->pluck('category')->unique()->values()->all();
        $trending = Post::published()->orderByDesc('views')->limit(4)->get();

        return view('posts.index', compact('posts', 'categories', 'activeCategories', 'featured', 'trending'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('published', true)
            ->where('status', Post::STATUS_PUBLISHED)
            ->with(['user', 'approvedComments'])
            ->firstOrFail();

        $post->increment('views');

        $related = Post::published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        return view('posts.show', compact('post', 'related'));
    }

    public function comment(Request $request, Post $post)
    {
        abort_unless($post->published && $post->status === Post::STATUS_PUBLISHED, 404);

        $request->validate([
            'body'  => 'required|max:1000',
            'name' => Auth::check() ? 'nullable|max:100' : 'required|max:100',
            'email' => Auth::check() ? 'nullable|email' : 'required|email',
        ]);

        $user = Auth::user();

        $post->comments()->create([
            'user_id' => $user?->id,
            'name'  => $user?->name ?? $request->name,
            'email' => $user?->email ?? $request->email,
            'body'  => $request->body,
        ]);

        return back()->with('success', 'Comment submitted and awaiting approval.');
    }
}
