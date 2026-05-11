@php
    $chapterLines = old(
        'chapters',
        isset($projectType)
            ? $projectType->templateChapters->sortBy('sort_order')->pluck('title')->implode("\n")
            : ''
    );
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="form-field md:col-span-2">
        <label class="form-label" for="name">Name *</label>
        <input
            class="form-input"
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $projectType->name ?? '') }}"
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
        >{{ old('description', $projectType->description ?? '') }}</textarea>
        @error('description')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-field md:col-span-2">
        <label class="form-label" for="chapters">Template Chapters *</label>
        <textarea
            class="form-input"
            id="chapters"
            name="chapters"
            rows="8"
            required
            placeholder="Write one chapter per line (ex: 1. Scope)"
        >{{ $chapterLines }}</textarea>
        <p class="text-xs text-gray-400 mt-2">These chapters are copied automatically when a user creates a project from this type.</p>
        @error('chapters')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-field md:col-span-2">
        <label class="form-check">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', isset($projectType) ? $projectType->is_active : true) ? 'checked' : '' }}
            >
            Active
        </label>
        @error('is_active')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>
