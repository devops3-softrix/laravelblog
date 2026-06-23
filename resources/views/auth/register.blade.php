@extends('layouts.app')
@section('title', 'Sign up')

@section('content')
<main class="container">
    <div class="auth-container">
        <div class="auth-box panel">
            <span class="eyebrow">Join the newsroom</span>
            <h1>Create your account</h1>
            <p class="muted" style="margin-top:0;">Readers can comment. Authors can submit posts for admin approval.</p>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Account type</label>
                    <select name="account_type" required>
                        <option value="user" {{ old('account_type') === 'user' ? 'selected' : '' }}>Reader</option>
                        <option value="author" {{ old('account_type') === 'author' ? 'selected' : '' }}>Author</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn" style="width:100%;">Create account</button>
            </form>

            <p class="muted" style="text-align:center;margin-bottom:0;">
                Already registered? <a href="{{ route('login') }}" class="read-more">Login</a>
            </p>
        </div>
    </div>
</main>
@endsection
