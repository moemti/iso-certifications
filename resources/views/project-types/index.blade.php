<x-layouts.app>
    <div class="list-page">
        <div class="list-shell">
            <div class="auth-card auth-card--wide list-card">
                <h1 class="auth-title">Project Types</h1>
                <p class="auth-desc">Manage project templates that users can create projects from.</p>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6 flex justify-end">
                    <a href="{{ route('project-types.create') }}" class="button button-primary">
                        Add Project Type
                    </a>
                </div>

                @if ($projectTypes->count() > 0)
                    <div class="list-table-wrap">
                        <table class="list-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Template Chapters</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projectTypes as $projectType)
                                    <tr>
                                        <td data-label="Name">{{ $projectType->name }}</td>
                                        <td data-label="Description">{{ \Illuminate\Support\Str::limit($projectType->description, 90) }}</td>
                                        <td data-label="Template Chapters">{{ $projectType->template_chapters_count }}</td>
                                        <td data-label="Status">{{ $projectType->is_active ? 'Active' : 'Inactive' }}</td>
                                        <td data-label="Actions">
                                            <div class="list-actions">
                                                <a href="{{ route('project-types.edit', $projectType) }}" class="text-blue-400 hover:text-blue-300">
                                                    Edit
                                                </a>
                                                <form action="{{ route('project-types.destroy', $projectType) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project type?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $projectTypes->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <p>No project types found. <a href="{{ route('project-types.create') }}" class="text-blue-400 hover:text-blue-300">Create one</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
