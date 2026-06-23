@extends('layouts.app')
@section('title', 'Login')

@section('content')
<main class="container">
    <div class="auth-container">
        <div class="auth-box panel">
            <span class="eyebrow">Welcome back</span>
            <h1>Login to PulsePress</h1>
            <p class="muted" style="margin-top:0;">Manage posts, approve comments, and continue your editorial work.</p>

            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:.55rem;">
                    <input type="checkbox" name="remember" id="remember" style="width:18px;height:18px;">
                    <label for="remember" style="margin:0;font-weight:600;">Remember me</label>
                </div>
                <button type="submit" class="btn" style="width:100%;">Login</button>
            </form>

            <p class="muted" style="text-align:center;margin-bottom:0;">
                New here? <a href="{{ route('register') }}" class="read-more">Create an account</a>
            </p>
            <p class="muted" style="text-align:center;font-size:.85rem;">Seed admin: admin@blog.com / password</p>
        </div>
    </div>
</main>
@endsection
