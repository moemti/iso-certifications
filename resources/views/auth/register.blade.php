<x-layouts.app :title="'Register'">
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Create an account</h1>
            <p class="auth-desc">Register your organisation to start managing certification projects.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-field">
                    <label class="form-label" for="name">Full name</label>
                    <input class="form-input" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" id="password" type="password" name="password" required autocomplete="new-password">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input class="form-input" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    @error('password_confirmation') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-actions">
                    <a class="form-link" href="{{ route('login') }}">Already registered?</a>
                    <button type="submit" class="button button-primary">Create account</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
