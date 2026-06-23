<aside class="sidebar">
    <p style="color:#98a2b3;font-size:.75rem;text-transform:uppercase;font-weight:800;margin:.25rem .8rem 1rem;">Workspace</p>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.posts') }}" class="{{ request()->routeIs('admin.posts') ? 'active' : '' }}">Posts</a>
    @if(auth()->user()->isAuthor())
        <a href="{{ route('admin.post.create') }}" class="{{ request()->routeIs('admin.post.create') ? 'active' : '' }}">New post</a>
    @endif
    <a href="{{ route('admin.comments') }}" class="{{ request()->routeIs('admin.comments') ? 'active' : '' }}">Comments</a>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">Users</a>
    @endif
    <div style="border-top:1px solid #263445;margin:1rem 0;"></div>
    <a href="{{ route('home') }}">View site</a>
</aside>
