<x-layouts.app>
    <div class="auth-page" style="padding-top: calc(var(--header-height, 74px) + 24px);">
        <div class="auth-card auth-card--wide" style="max-width: 920px;">
            <h1 class="auth-title">Edit Project Type: {{ $projectType->name }}</h1>
            <p class="auth-desc">Update this project type and chapter template.</p>

            <form action="{{ route('project-types.update', $projectType) }}" method="POST" class="mt-6">
                @csrf
                @method('PATCH')

                @include('project-types.form-fields')

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="button button-primary flex-1">
                        Update Project Type
                    </button>
                    <a href="{{ route('project-types.index') }}" class="button bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded flex-1 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
