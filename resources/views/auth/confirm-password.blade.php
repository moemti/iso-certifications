<x-layouts.app :title="'Confirm Password'">
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Confirm your password</h1>
            <p class="auth-desc">This is a secure area. Please confirm your password before continuing.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-field">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" id="password" type="password" name="password" required autocomplete="current-password">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="button button-primary" style="margin-left:auto">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
