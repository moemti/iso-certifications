<x-layouts.app :title="'Welcome'">
    <div class="page-main">
        <section class="hero" aria-labelledby="main-title">
            <div class="eyebrow">Now live</div>
            <h1 id="main-title" class="hero-title">A professional platform for certification support.</h1>
            <p class="lead">
                Our certification portal is live and ready for your organisation. Manage certification
                projects, prepare for audits, maintain management system documentation, and stay
                compliant — all in one place.
            </p>

            <div class="actions">
                @auth
                    <a class="button button-primary" href="{{ url('/dashboard') }}">Go to dashboard</a>
                @else
                    <a class="button button-primary" href="{{ route('register') }}">Get started</a>
                    <a class="button button-secondary" href="{{ route('login') }}">Sign in</a>
                @endauth
            </div>

            <div class="features" aria-label="Main service areas">
                <div class="feature">
                    <strong>Certification Types</strong>
                    <span>Guidance and project management for quality, environmental, health and safety, security, and compliance certifications.</span>
                </div>
                <div class="feature">
                    <strong>Audit Preparation</strong>
                    <span>Structured chapter-based projects for documentation, internal checks, and full audit readiness.</span>
                </div>
                <div class="feature">
                    <strong>Team Collaboration</strong>
                    <span>Invite team members, assign roles, and control who can view or edit each part of your certification project.</span>
                </div>
            </div>
        </section>

        <aside class="info-card" aria-label="Platform information">
            <div class="launch-box">
                <div class="label">Platform status</div>
                <div class="value">Live</div>
            </div>

            <div class="list">
                <div class="list-item">
                    <span class="check">&check;</span>
                    <span>Register your organisation and select a certification type to begin.</span>
                </div>
                <div class="list-item">
                    <span class="check">&check;</span>
                    <span>Work through structured chapters, upload evidence, and track completion status.</span>
                </div>
                <div class="list-item">
                    <span class="check">&check;</span>
                    <span>Generate a final document package ready for submission to your certification body.</span>
                </div>
            </div>

            <div class="contact">
                <p>
                    For questions about certification support, documentation, or audit preparation,
                    contact us at:
                </p>
                <a href="mailto:admin@iso-certifications.net">admin@iso-certifications.net</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
