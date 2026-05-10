<section>
    <header class="mb-8">
        <h2 class="auth-title">{{ __('Profile Information') }}</h2>
        <p class="auth-desc">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="form-field">
            <label class="form-label" for="name">{{ __('Name') }}</label>
            <input class="form-input" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-field">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input class="form-input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="auth-desc" style="margin-top: 12px; margin-bottom: 0;">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="form-link" type="submit" style="padding: 0; border: 0; background: transparent;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="form-error" style="color: var(--accent-2); margin-top: 10px;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">{{ __('Save changes') }}</button>

            @if (session('status') === 'profile-updated')
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
