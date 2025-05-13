@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow" style="width: 400px;">

        @include('notices')
        <div class="text-center">
            <img src="{{ asset('/images/branding/logo.png') }}" alt="Logo" class="mb-3" width="150">
            <h4 class="mb-3">Two-Factor Authentication</h4>
            <p class="text-muted">Enter the code sent to your email or phone. The code will Expire in 10 mins</p>
        </div>

        @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf
            <div class="mb-3">
                <label for="code" class="form-label">Enter the Code</label>
                <input type="text" name="code" class="form-control" autofocus placeholder="Enter the code">
                @error('code')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Verify</button>
            </div>


        </form>

        <div class="d-grid gap-2">

            <form method="POST" action="{{ route('2fa.resend') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-block mt-3">Resend Code</button>
            </form>
            <a href="{{ route('logout') }}" class="btn btn-secondary"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Go Back
            </a>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>
@endsection