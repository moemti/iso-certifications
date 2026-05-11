@php
    $chapterRows = old('chapters');

    if (!is_array($chapterRows)) {
        $chapterRows = isset($projectType)
            ? $projectType->templateChapters
                ->sortBy('sort_order')
                ->map(static function ($chapter): array {
                    return [
                        'title' => $chapter->title,
                        'description' => $chapter->description,
                        'admin_default_text' => $chapter->admin_default_text,
                        'blocks_definition' => $chapter->blocks
                            ->sortBy('sort_order')
                            ->map(static function ($block): string {
                                if ($block->block_type === 'image') {
                                    return 'image_user|' . ($block->is_required ? 'required' : 'optional') . '|' . ($block->caption_position ?? 'below') . '|' . ($block->prompt_text ?? 'Upload an image');
                                }

                                if ($block->editable_by === 'admin') {
                                    return 'text_admin|' . ($block->prompt_text ?? '') . '|' . ($block->admin_content ?? '');
                                }

                                return 'text_user|' . ($block->is_required ? 'required' : 'optional') . '|' . ($block->prompt_text ?? 'Complete this section');
                            })
                            ->implode("\n"),
                        'is_required' => $chapter->is_required,
                        'is_user_editable' => $chapter->is_user_editable,
                        'is_active' => $chapter->is_active,
                    ];
                })
                ->values()
                ->all()
            : [];
    }

    if (count($chapterRows) === 0) {
        $chapterRows = [[
            'title' => '',
            'description' => '',
            'admin_default_text' => '',
            'blocks_definition' => "text_user|required|Complete this section",
            'is_required' => true,
            'is_user_editable' => true,
            'is_active' => true,
        ]];
    }
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
        <div class="flex items-center justify-between gap-4 mb-3">
            <label class="form-label" style="margin-bottom: 0;" for="chapter-builder">Template Chapters *</label>
            <button type="button" id="add-chapter-row" class="button button-secondary" style="min-height: 36px; padding: 0 14px;">Add Chapter</button>
        </div>

        @error('chapters')
            <span class="form-error">{{ $message }}</span>
        @enderror

        <div id="chapter-builder" class="space-y-4">
            @foreach ($chapterRows as $index => $chapter)
                <div class="rounded-xl border border-slate-600/60 bg-slate-900/40 p-4 chapter-row" data-row-index="{{ $index }}">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                            <label class="form-label">Chapter Title *</label>
                            <input class="form-input" type="text" name="chapters[{{ $index }}][title]" value="{{ $chapter['title'] ?? '' }}" maxlength="255" required>
                        </div>

                        <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                            <label class="form-label">Admin Guidance (Optional)</label>
                            <input class="form-input" type="text" name="chapters[{{ $index }}][description]" value="{{ $chapter['description'] ?? '' }}" maxlength="2000" placeholder="Short instruction visible to user">
                        </div>

                        <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                            <label class="form-label">Default Template Text</label>
                            <textarea class="form-input" name="chapters[{{ $index }}][admin_default_text]" rows="5" maxlength="10000" placeholder="This text appears like Word template content in the user project document.">{{ $chapter['admin_default_text'] ?? '' }}</textarea>
                        </div>

                        <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                            <label class="form-label">Template Blocks Definition</label>
                            <textarea class="form-input" name="chapters[{{ $index }}][blocks_definition]" rows="6" maxlength="30000" placeholder="text_admin|Intro|Admin paragraph&#10;text_user|required|User paragraph prompt&#10;image_user|required|below|Upload process screenshot">{{ $chapter['blocks_definition'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-400 mt-2">Line formats: <strong>text_admin|prompt|text</strong>, <strong>text_user|required|prompt</strong>, <strong>image_user|required|above|prompt</strong>.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 md:col-span-2">
                            <label class="form-check" style="margin-top: 0;">
                                <input type="hidden" name="chapters[{{ $index }}][is_required]" value="0">
                                <input type="checkbox" name="chapters[{{ $index }}][is_required]" value="1" {{ !empty($chapter['is_required']) ? 'checked' : '' }}>
                                Required section
                            </label>

                            <label class="form-check" style="margin-top: 0;">
                                <input type="hidden" name="chapters[{{ $index }}][is_user_editable]" value="0">
                                <input type="checkbox" name="chapters[{{ $index }}][is_user_editable]" value="1" {{ !empty($chapter['is_user_editable']) ? 'checked' : '' }}>
                                User can edit
                            </label>

                            <label class="form-check" style="margin-top: 0;">
                                <input type="hidden" name="chapters[{{ $index }}][is_active]" value="0">
                                <input type="checkbox" name="chapters[{{ $index }}][is_active]" value="1" {{ !empty($chapter['is_active']) ? 'checked' : '' }}>
                                Active
                            </label>
                        </div>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="button" class="button" style="min-height: 34px; padding: 0 12px; background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fecaca;" data-remove-row>Remove Chapter</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template id="chapter-row-template">
            <div class="rounded-xl border border-slate-600/60 bg-slate-900/40 p-4 chapter-row" data-row-index="__INDEX__">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                        <label class="form-label">Chapter Title *</label>
                        <input class="form-input" type="text" name="chapters[__INDEX__][title]" maxlength="255" required>
                    </div>

                    <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                        <label class="form-label">Admin Guidance (Optional)</label>
                        <input class="form-input" type="text" name="chapters[__INDEX__][description]" maxlength="2000" placeholder="Short instruction visible to user">
                    </div>

                    <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                        <label class="form-label">Default Template Text</label>
                        <textarea class="form-input" name="chapters[__INDEX__][admin_default_text]" rows="5" maxlength="10000" placeholder="This text appears like Word template content in the user project document."></textarea>
                    </div>

                    <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                        <label class="form-label">Template Blocks Definition</label>
                        <textarea class="form-input" name="chapters[__INDEX__][blocks_definition]" rows="6" maxlength="30000" placeholder="text_admin|Intro|Admin paragraph&#10;text_user|required|User paragraph prompt&#10;image_user|required|below|Upload process screenshot">text_user|required|Complete this section</textarea>
                        <p class="text-xs text-gray-400 mt-2">Line formats: <strong>text_admin|prompt|text</strong>, <strong>text_user|required|prompt</strong>, <strong>image_user|required|above|prompt</strong>.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 md:col-span-2">
                        <label class="form-check" style="margin-top: 0;">
                            <input type="hidden" name="chapters[__INDEX__][is_required]" value="0">
                            <input type="checkbox" name="chapters[__INDEX__][is_required]" value="1" checked>
                            Required section
                        </label>

                        <label class="form-check" style="margin-top: 0;">
                            <input type="hidden" name="chapters[__INDEX__][is_user_editable]" value="0">
                            <input type="checkbox" name="chapters[__INDEX__][is_user_editable]" value="1" checked>
                            User can edit
                        </label>

                        <label class="form-check" style="margin-top: 0;">
                            <input type="hidden" name="chapters[__INDEX__][is_active]" value="0">
                            <input type="checkbox" name="chapters[__INDEX__][is_active]" value="1" checked>
                            Active
                        </label>
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button type="button" class="button" style="min-height: 34px; padding: 0 12px; background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fecaca;" data-remove-row>Remove Chapter</button>
                    </div>
                </div>
            </div>
        </template>
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

<script>
    (function () {
        const container = document.getElementById('chapter-builder');
        const addButton = document.getElementById('add-chapter-row');
        const template = document.getElementById('chapter-row-template');

        if (!container || !addButton || !template) {
            return;
        }

        let rowIndex = container.querySelectorAll('.chapter-row').length;

        function bindRemoveButtons() {
            container.querySelectorAll('[data-remove-row]').forEach((button) => {
                button.onclick = function () {
                    const rows = container.querySelectorAll('.chapter-row');
                    if (rows.length <= 1) {
                        return;
                    }

                    button.closest('.chapter-row')?.remove();
                };
            });
        }

        addButton.addEventListener('click', function () {
            const html = template.innerHTML.replaceAll('__INDEX__', String(rowIndex));
            rowIndex += 1;
            container.insertAdjacentHTML('beforeend', html);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    })();
</script>
