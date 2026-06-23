@extends('layouts.app')
@section('title', 'Users')

@section('content')
<div class="admin-layout">
    @include('admin.partials.sidebar')

    <main class="admin-content">
        <div class="admin-header">
            <div>
                <span class="eyebrow">Access control</span>
                <h1 style="margin:.2rem 0 0;">Users</h1>
                <p class="muted" style="margin:.2rem 0 0;">Promote readers to authors or grant admin access.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Posts</th>
                        <th>Joined</th>
                        <th>Change role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong><br>
                                <span class="muted">{{ $user->email }}</span>
                            </td>
                            <td><span class="badge {{ $user->role === 'admin' ? 'published' : ($user->role === 'author' ? 'pending' : 'draft') }}">{{ ucfirst($user->role) }}</span></td>
                            <td>{{ $user->posts_count }}</td>
                            <td class="muted">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.user.role', $user) }}" class="actions">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" style="width:auto;min-width:130px;">
                                            @foreach(['user' => 'Reader', 'author' => 'Author', 'admin' => 'Admin'] as $role => $label)
                                                <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm">Update</button>
                                    </form>
                                @else
                                    <span class="muted">Current account</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>
    </main>
</div>
@endsection
