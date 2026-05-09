<x-layouts.app :title="'Verify Email'">
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-title">Verify your email</h1>
            <p class="auth-desc">
                Thanks for signing up! Please verify your email address by clicking the link we sent to your inbox.
                If you didn't receive it, we can send another.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success">A new verification link has been sent to your email address.</div>
            @endif

            <div style="display:flex;flex-direction:column;gap:12px;margin-top:8px;">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="button button-primary" style="width:100%">Resend verification email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button button-secondary" style="width:100%">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
