<x-layouts.app :title="'Dashboard'">
    <div class="page-main">
        <section class="hero" aria-labelledby="dashboard-title">
            <h2 id="dashboard-title" class="section-title">
                Here are your certification projects. Track progress, invite your team, and manage all your compliance work in one place.
            </h2>

            <!-- Project List -->
            <div class="project-list" aria-label="Your Projects">
                <!-- Example: Loop through projects -->
                @php // Replace with real project loop
                $projects = [
                    [
                        'name' => 'ISO 9001:2024 Implementation',
                        'company' => 'Acme Corp',
                        'type' => 'ISO 9001',
                        'registration_status' => 'approved',
                        'progress_status' => 'partial_completed',
                    ],
                    [
                        'name' => 'ISO 27001:2026 Readiness',
                        'company' => 'Acme Corp',
                        'type' => 'ISO 27001',
                        'registration_status' => 'pending_approval',
                        'progress_status' => 'partial_completed',
                    ],
                ];
                @endphp
                @if (count($projects))
                    <div class="project-table">
                        <div class="project-table-head">
                            <div>Project</div>
                            <div>Company</div>
                            <div>Type</div>
                            <div>Status</div>

                            <div></div>
                        </div>
                        @foreach ($projects as $project)
                            <div class="project-table-row">
                                <div class="project-name"><a href="#">{{ $project['name'] }}</a></div>
                                <div>{{ $project['company'] }}</div>
                                <div>{{ $project['type'] }}</div>
                                <div>
                                    <span class="status-badge status-{{ $project['registration_status'] }}">
                                        {{ ucfirst(str_replace('_', ' ', $project['registration_status'])) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📁</div>
                        <div class="empty-title">No projects yet</div>
                        <div class="empty-desc">Start your first certification project to begin your compliance journey.</div>
                        <a class="button button-primary" href="#">Start a new project</a>
                    </div>
                @endif
            </div>
        </section>

        <aside class="info-card" aria-label="Quick actions and account info">
            <div class="launch-box">
                <div class="label">Quick Actions</div>
                <div class="value">&nbsp;</div>
            </div>
            
            <div class="contact">
                <p>Need help or have questions?</p>
                <a href="mailto:admin@iso-certifications.net">admin@iso-certifications.net</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
