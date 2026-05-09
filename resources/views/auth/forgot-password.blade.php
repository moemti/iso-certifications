<x-layouts.app :title="'Reset Password'">
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Reset password</h1>
            <p class="auth-desc">Enter your email address and we'll send you a link to reset your password.</p>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-actions">
                    <a class="form-link" href="{{ route('login') }}">Back to sign in</a>
                    <button type="submit" class="button button-primary">Send reset link</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
