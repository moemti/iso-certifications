<x-layouts.app :title="'Reset Password'">
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Choose a new password</h1>
            <p class="auth-desc">Enter and confirm your new password below.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-input" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password">New password</label>
                    <input class="form-input" id="password" type="password" name="password" required autocomplete="new-password">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input class="form-input" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    @error('password_confirmation') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="button button-primary" style="margin-left:auto">Reset password</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
