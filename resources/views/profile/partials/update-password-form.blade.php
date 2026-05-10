<section>
    <header class="mb-8">
        <h2 class="auth-title">{{ __('Update Password') }}</h2>
        <p class="auth-desc">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-field">
            <label class="form-label" for="update_password_current_password">{{ __('Current Password') }}</label>
            <input class="form-input" id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
            <div class="form-error">@error('current_password', 'updatePassword') {{ $message }} @enderror</div>
        </div>

        <div class="form-field">
            <label class="form-label" for="update_password_password">{{ __('New Password') }}</label>
            <input class="form-input" id="update_password_password" name="password" type="password" autocomplete="new-password">
            <div class="form-error">@error('password', 'updatePassword') {{ $message }} @enderror</div>
        </div>

        <div class="form-field">
            <label class="form-label" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input class="form-input" id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            <div class="form-error">@error('password_confirmation', 'updatePassword') {{ $message }} @enderror</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">{{ __('Save changes') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="auth-desc"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
