<x-layouts.app :title="'Dashboard'">
    <div class="page-main">
        <section class="hero" aria-labelledby="dashboard-title">
            <div class="eyebrow">Dashboard</div>
            <h1 id="dashboard-title" class="hero-title">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="lead">
                Manage your certification projects, collaborate with your team, and track your progress toward audit readiness and compliance.
            </p>

            <div class="actions">
                <a class="button button-primary" href="#">Start a new project</a>
                <a class="button button-secondary" href="#">View all projects</a>
            </div>

            <div class="features" aria-label="Your tools">
                <div class="feature">
                    <strong>Project Overview</strong>
                    <span>See all your certification projects and their current status in one place.</span>
                </div>
                <div class="feature">
                    <strong>Team Management</strong>
                    <span>Invite colleagues, assign roles, and collaborate securely on documentation and tasks.</span>
                </div>
                <div class="feature">
                    <strong>Deliverables & Evidence</strong>
                    <span>Upload, organize, and access all required documents and evidence for audits.</span>
                </div>
            </div>
        </section>

        <aside class="info-card" aria-label="Account information">
            <div class="launch-box">
                <div class="label">Account</div>
                <div class="value">Active</div>
            </div>

            <div class="list">
                <div class="list-item">
                    <span class="check">&check;</span>
                    <span>Logged in as <strong>{{ auth()->user()->email }}</strong></span>
                </div>
                <div class="list-item">
                    <span class="check">&check;</span>
                    <span>Access your projects and team from the dashboard.</span>
                </div>
                <div class="list-item">
                    <span class="check">&check;</span>
                    <span>Update your profile and account settings anytime.</span>
                </div>
            </div>

            <div class="contact">
                <p>
                    Need help or have questions? Contact support:
                </p>
                <a href="mailto:admin@iso-certifications.net">admin@iso-certifications.net</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
