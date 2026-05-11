<x-layouts.app :title="'Dashboard'">
    <div class="page-main">
        <section class="hero" aria-labelledby="dashboard-title">
            <h2 id="dashboard-title" class="section-title">
                Here are your certification projects. Track progress, invite your team, and manage all your compliance work in one place.
            </h2>

            <!-- Project List -->
            <div class="project-list" aria-label="Your Projects">
                @if (session('success'))
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error" style="margin-bottom: 20px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if (count($projects))
                    <div class="project-table">
                        <div class="project-table-head">
                            <div>Project</div>
                            <div>Type</div>
                            <div>Chapters</div>
                            <div>Status</div>
                        </div>
                        @foreach ($projects as $project)
                            <div class="project-table-row">
                                <div class="project-name"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></div>
                                <div>{{ $project->projectType->name }}</div>
                                <div>{{ $project->chapters_count }}</div>
                                <div>
                                    <span class="status-badge">
                                        {{ $project->is_active ? 'Active' : 'Inactive' }}
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
                        <a class="button button-primary" href="{{ route('projects.create') }}">Start a new project</a>
                    </div>
                @endif
            </div>
        </section>

        <aside class="info-card" aria-label="Quick actions and account info">
            <div class="launch-box" style="padding-top: 18px; padding-bottom: 18px;">
                <div style="margin-top: 0; display: flex; flex-direction: column; gap: 12px;">
                    <a href="{{ route('projects.create') }}" class="button button-primary" style="width: 100%; text-align: center; display: flex; align-items: center; justify-content: center; min-height: 50px;">
                        New Project
                    </a>
                    @if (auth()->user()->IsAdmin)
                        <a href="{{ route('partners.index') }}" class="button button-primary" style="width: 100%; text-align: center; display: flex; align-items: center; justify-content: center; min-height: 50px;">
                            Partners
                        </a>
                        <a href="{{ route('project-types.index') }}" class="button button-primary" style="width: 100%; text-align: center; display: flex; align-items: center; justify-content: center; min-height: 50px;">
                            Project Types
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="contact">
                <p>Need help or have questions?</p>
                <a href="mailto:admin@iso-certifications.net">admin@iso-certifications.net</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
