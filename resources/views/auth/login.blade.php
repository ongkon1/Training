<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login &middot; {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('asset/css/theme.css') }}?v={{ filemtime(public_path('asset/css/theme.css')) }}" rel="stylesheet">
</head>
<body class="auth-page">
<div class="container auth-shell">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-lg-6 d-none d-lg-block auth-intro">
            <div class="auth-mark"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i></div>
            <div class="auth-eyebrow">Your learning workspace</div>
            <h2>Build confidence.<br>See your progress.</h2>
            <p>A shared space for training, assessments, and practical feedback.</p>
            <div class="auth-feature"><i class="bi bi-mic" aria-hidden="true"></i><span>Practice through language training</span></div>
            <div class="auth-feature"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i><span>Review results and track progress</span></div>
            <div class="auth-feature"><i class="bi bi-chat-square-text" aria-hidden="true"></i><span>Learn from personalized feedback</span></div>
        </div>
        <div class="col-md-8 col-lg-5 offset-lg-1 auth-panel">
            <div class="text-center mb-4">
                <i class="bi bi-mortarboard-fill fs-1" style="color: var(--pia-accent);"></i>
                <h1 class="h4 mt-2 gradient-text">{{ config('app.name') }}</h1>
                <p class="text-muted small mb-0">Sign in to continue</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @include('partials._errors')

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-3 mb-0">
                Accounts are created by trainers. Contact your trainer if you cannot sign in.
            </p>
        </div>
    </div>
</div>
</body>
</html>
