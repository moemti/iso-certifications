<x-layouts.app>
    <div class="auth-page" style="padding-top: calc(var(--header-height, 74px) + 24px);">
        <div class="auth-card auth-card--wide" style="max-width: 920px;">
            <h1 class="auth-title">Create New Project</h1>
            <p class="auth-desc">Choose a project type and we will generate all chapter sections from its template.</p>

            @if ($projectTypes->isEmpty())
                <div class="alert alert-error">
                    No active project types are available yet. Please ask an administrator to create one.
                </div>
            @else
                <form action="{{ route('projects.store') }}" method="POST" class="mt-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="form-field md:col-span-2">
                            <label class="form-label" for="name">Project Name *</label>
                            <input
                                class="form-input"
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                maxlength="255"
                            >
                            @error('name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field md:col-span-2">
                            <label class="form-label" for="description">Description</label>
                            <textarea
                                class="form-input"
                                id="description"
                                name="description"
                                rows="4"
                                maxlength="2000"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field md:col-span-2">
                            <label class="form-label" for="project_type_id">Project Type *</label>
                            <select class="form-input" id="project_type_id" name="project_type_id" required>
                                <option value="">Select a project type</option>
                                @foreach ($projectTypes as $projectType)
                                    <option value="{{ $projectType->id }}" {{ old('project_type_id') == $projectType->id ? 'selected' : '' }}>
                                        {{ $projectType->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_type_id')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field md:col-span-2">
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                Active
                            </label>
                            @error('is_active')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 rounded-lg border border-slate-600/50 bg-slate-900/40 p-4">
                        <h2 class="text-sm font-semibold text-gray-200">Template chapters preview</h2>
                        <p class="text-xs text-gray-400 mt-1">When you submit, these chapters will be created in your project.</p>
                        <ul id="project-type-chapter-preview" class="mt-3 list-disc pl-6 text-sm text-gray-300"></ul>
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="button button-primary flex-1">
                            Create Project
                        </button>
                        <a href="{{ route('dashboard') }}" class="button bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex-1 text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @if ($projectTypes->isNotEmpty())
        <script>
            const projectTypes = @json(
                $projectTypes->mapWithKeys(fn ($type) => [
                    (string) $type->id => $type->templateChapters->pluck('title')->values(),
                ])
            );

            const typeSelect = document.getElementById('project_type_id');
            const previewList = document.getElementById('project-type-chapter-preview');

            function renderChapterPreview() {
                const selectedId = typeSelect.value;
                const chapters = projectTypes[selectedId] || [];

                previewList.innerHTML = '';

                if (chapters.length === 0) {
                    const emptyItem = document.createElement('li');
                    emptyItem.textContent = 'No chapters available for this type.';
                    previewList.appendChild(emptyItem);
                    return;
                }

                chapters.forEach((chapter) => {
                    const listItem = document.createElement('li');
                    listItem.textContent = chapter;
                    previewList.appendChild(listItem);
                });
            }

            typeSelect.addEventListener('change', renderChapterPreview);
            renderChapterPreview();
        </script>
    @endif
</x-layouts.app>
