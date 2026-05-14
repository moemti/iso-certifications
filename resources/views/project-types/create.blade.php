<x-layouts.app>
    <div class="doc-page-shell">
        <form action="{{ route('project-types.store') }}" method="POST">
            @csrf

            @include('project-types.form-fields')

            <div class="word-editor-footer-actions">
                <button type="submit" class="button button-primary">Add Project Type</button>
                <a href="{{ route('project-types.index') }}" class="button button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
