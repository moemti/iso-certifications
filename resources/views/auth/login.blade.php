<x-layouts.app :title="'Sign in'">
    <div class="auth-page" style="padding-top: calc(var(--header-height, 74px) + 24px);">
        <div class="auth-card">
            <h1 class="auth-title">Sign in</h1>
            <p class="auth-desc">Welcome back. Sign in to your account to continue.</p>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" id="password" type="password" name="password" required autocomplete="current-password">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <label class="form-check">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>

                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a class="form-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                    <button type="submit" class="button button-primary">Sign in</button>
                </div>
            </form>

            <p class="auth-footer">
                Don't have an account? <a href="{{ route('register') }}">Register</a>
            </p>
        </div>
    </div>
</x-layouts.app>
