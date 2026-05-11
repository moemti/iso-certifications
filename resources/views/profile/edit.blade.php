<x-layouts.app :title="__('Profile')">
    <div class="auth-page profile-page" style="padding-top: calc(var(--header-height, 74px) + 24px);">
        <div class="profile-stack">
            <div class="auth-card auth-card--wide">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="auth-card auth-card--wide">
                @include('profile.partials.update-password-form')
            </div>

            <div class="auth-card auth-card--wide">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-layouts.app>
