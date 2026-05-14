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

    $parseBlocksDefinition = static function (?string $definition): array {
        $lines = preg_split('/\r\n|\r|\n/', (string) $definition) ?: [];
        $blocks = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            $type = strtolower($parts[0] ?? 'text_user');

            if ($type === 'text_admin') {
                $blocks[] = [
                    'type' => 'text_admin',
                    'required' => false,
                    'caption' => 'below',
                    'prompt' => $parts[1] ?? '',
                    'admin_content' => $parts[2] ?? '',
                ];
                continue;
            }

            if ($type === 'image_user') {
                $blocks[] = [
                    'type' => 'image_user',
                    'required' => strtolower($parts[1] ?? '') === 'required',
                    'caption' => in_array(strtolower($parts[2] ?? ''), ['above', 'below'], true) ? strtolower($parts[2]) : 'below',
                    'prompt' => $parts[3] ?? ($parts[2] ?? 'Upload an image'),
                    'admin_content' => '',
                ];
                continue;
            }

            $blocks[] = [
                'type' => 'text_user',
                'required' => strtolower($parts[1] ?? '') === 'required',
                'caption' => 'below',
                'prompt' => $parts[2] ?? ($parts[1] ?? 'Complete this section'),
                'admin_content' => '',
            ];
        }

        if (count($blocks) > 0) {
            return $blocks;
        }

        return [[
            'type' => 'text_user',
            'required' => true,
            'caption' => 'below',
            'prompt' => 'Complete this section',
            'admin_content' => '',
        ]];
    };
@endphp

<div class="word-editor-shell" data-project-type-editor>
    <header class="word-editor-header">
        <div>
            <h1 class="word-editor-title">{{ isset($projectType) ? 'Edit Project Type: ' . $projectType->name : 'Add Project Type' }}</h1>
            <p class="word-editor-subtitle">Build chapter and block structure like a Word document, with a clickable left overview.</p>
        </div>
        <div class="doc-view-controls">
            <button type="button" class="doc-view-button is-active" data-editor-mode="selected">Selected page</button>
            <button type="button" class="doc-view-button" data-editor-mode="all">All pages</button>
        </div>
    </header>

    <div class="word-editor-layout">
        <aside class="word-editor-sidebar" aria-label="Chapter Overview">
            <div class="word-editor-sidebar-head">
                <h2>Overview</h2>
                <button type="button" class="button button-secondary word-sidebar-add" id="add-chapter-row">Add Chapter</button>
            </div>

            <ul class="doc-chapter-nav" id="chapter-outline-nav">
                <li>
                    <button type="button" class="doc-nav-link is-active" data-outline-target="all">All pages</button>
                </li>
            </ul>
        </aside>

        <section class="word-editor-main" aria-label="Project Type Template Document">
            <div class="word-page-stack" id="chapter-builder">
                <article class="word-page word-page--meta" data-meta-page>
                    <h3 class="word-page-meta-title">Template Information</h3>

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
                            <label class="form-check" style="margin-top: 0;">
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
                </article>

                @foreach ($chapterRows as $index => $chapter)
                    @php
                        $chapterBlocks = $parseBlocksDefinition($chapter['blocks_definition'] ?? null);
                    @endphp
                    <article class="word-page chapter-row" data-row-index="{{ $index }}" data-page-id="chapter-page-{{ $index }}">
                        <div class="word-page-header">
                            <h3 class="word-page-title">Chapter <span class="chapter-order">{{ $index + 1 }}</span></h3>
                            <button type="button" class="button word-danger-action" data-remove-row>Delete Chapter</button>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                                <label class="form-label">Chapter Title *</label>
                                <input class="form-input" type="text" name="chapters[{{ $index }}][title]" value="{{ $chapter['title'] ?? '' }}" maxlength="255" required data-chapter-title>
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
                                <div class="word-blocks-header">
                                    <label class="form-label" style="margin-bottom: 0;">Chapter Blocks</label>
                                    <button type="button" class="button button-secondary word-sidebar-add" data-add-block>Add Block</button>
                                </div>

                                <div class="pt-block-list" data-block-list>
                                    @foreach ($chapterBlocks as $blockIndex => $block)
                                        <div class="pt-block-row" data-block-index="{{ $blockIndex }}">
                                            <div class="pt-block-top">
                                                <span class="pt-block-label">Block <span class="block-order">{{ $blockIndex + 1 }}</span></span>
                                                <button type="button" class="button word-danger-action word-danger-action--small" data-remove-block>Delete Block</button>
                                            </div>

                                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                <div class="form-field" style="margin-bottom: 0;">
                                                    <label class="form-label">Type</label>
                                                    <select class="form-input pt-block-type" data-block-type>
                                                        <option value="text_user" {{ ($block['type'] ?? 'text_user') === 'text_user' ? 'selected' : '' }}>Text - User editable</option>
                                                        <option value="text_admin" {{ ($block['type'] ?? '') === 'text_admin' ? 'selected' : '' }}>Text - Admin fixed</option>
                                                        <option value="image_user" {{ ($block['type'] ?? '') === 'image_user' ? 'selected' : '' }}>Image - User upload</option>
                                                    </select>
                                                </div>

                                                <div class="form-field pt-block-required-wrap" style="margin-bottom: 0;">
                                                    <label class="form-check" style="margin-top: 24px;">
                                                        <input type="checkbox" class="pt-block-required" {{ !empty($block['required']) ? 'checked' : '' }}>
                                                        Required
                                                    </label>
                                                </div>

                                                <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                                                    <label class="form-label">Prompt</label>
                                                    <input type="text" class="form-input pt-block-prompt" value="{{ $block['prompt'] ?? '' }}" maxlength="2000" placeholder="Prompt shown to user">
                                                </div>

                                                <div class="form-field md:col-span-2 pt-block-admin-wrap" style="margin-bottom: 0;">
                                                    <label class="form-label">Admin Text</label>
                                                    <textarea class="form-input pt-block-admin" rows="3" maxlength="10000" placeholder="Static admin paragraph">{{ $block['admin_content'] ?? '' }}</textarea>
                                                </div>

                                                <div class="form-field pt-block-caption-wrap" style="margin-bottom: 0;">
                                                    <label class="form-label">Caption Position</label>
                                                    <select class="form-input pt-block-caption">
                                                        <option value="above" {{ ($block['caption'] ?? 'below') === 'above' ? 'selected' : '' }}>Above image</option>
                                                        <option value="below" {{ ($block['caption'] ?? 'below') === 'below' ? 'selected' : '' }}>Below image</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <textarea class="form-input" name="chapters[{{ $index }}][blocks_definition]" rows="1" style="display: none;" data-blocks-definition>{{ $chapter['blocks_definition'] ?? '' }}</textarea>
                                <p class="text-xs text-gray-400 mt-2">Blocks are exported automatically to template format at save.</p>
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
                        </div>
                    </article>
                @endforeach
            </div>

            @error('chapters')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </section>
    </div>

    <template id="chapter-row-template">
        <article class="word-page chapter-row" data-row-index="__INDEX__" data-page-id="chapter-page-__INDEX__">
            <div class="word-page-header">
                <h3 class="word-page-title">Chapter <span class="chapter-order">__INDEX_ONE__</span></h3>
                <button type="button" class="button word-danger-action" data-remove-row>Delete Chapter</button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                    <label class="form-label">Chapter Title *</label>
                    <input class="form-input" type="text" name="chapters[__INDEX__][title]" maxlength="255" required data-chapter-title>
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
                    <div class="word-blocks-header">
                        <label class="form-label" style="margin-bottom: 0;">Chapter Blocks</label>
                        <button type="button" class="button button-secondary word-sidebar-add" data-add-block>Add Block</button>
                    </div>

                    <div class="pt-block-list" data-block-list>
                        __BLOCK_ROW__
                    </div>

                    <textarea class="form-input" name="chapters[__INDEX__][blocks_definition]" rows="1" style="display: none;" data-blocks-definition>text_user|required|Complete this section</textarea>
                    <p class="text-xs text-gray-400 mt-2">Blocks are exported automatically to template format at save.</p>
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
            </div>
        </article>
    </template>

    <template id="block-row-template">
        <div class="pt-block-row" data-block-index="__BLOCK_INDEX__">
            <div class="pt-block-top">
                <span class="pt-block-label">Block <span class="block-order">__BLOCK_ORDER__</span></span>
                <button type="button" class="button word-danger-action word-danger-action--small" data-remove-block>Delete Block</button>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="form-field" style="margin-bottom: 0;">
                    <label class="form-label">Type</label>
                    <select class="form-input pt-block-type" data-block-type>
                        <option value="text_user" selected>Text - User editable</option>
                        <option value="text_admin">Text - Admin fixed</option>
                        <option value="image_user">Image - User upload</option>
                    </select>
                </div>

                <div class="form-field pt-block-required-wrap" style="margin-bottom: 0;">
                    <label class="form-check" style="margin-top: 24px;">
                        <input type="checkbox" class="pt-block-required" checked>
                        Required
                    </label>
                </div>

                <div class="form-field md:col-span-2" style="margin-bottom: 0;">
                    <label class="form-label">Prompt</label>
                    <input type="text" class="form-input pt-block-prompt" maxlength="2000" placeholder="Prompt shown to user">
                </div>

                <div class="form-field md:col-span-2 pt-block-admin-wrap" style="margin-bottom: 0;">
                    <label class="form-label">Admin Text</label>
                    <textarea class="form-input pt-block-admin" rows="3" maxlength="10000" placeholder="Static admin paragraph"></textarea>
                </div>

                <div class="form-field pt-block-caption-wrap" style="margin-bottom: 0;">
                    <label class="form-label">Caption Position</label>
                    <select class="form-input pt-block-caption">
                        <option value="above">Above image</option>
                        <option value="below" selected>Below image</option>
                    </select>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    (function () {
        const root = document.querySelector('[data-project-type-editor]');
        if (!root) {
            return;
        }

        const chapterBuilder = root.querySelector('#chapter-builder');
        const chapterOutline = root.querySelector('#chapter-outline-nav');
        const addChapterButton = root.querySelector('#add-chapter-row');
        const chapterTemplate = root.querySelector('#chapter-row-template');
        const blockTemplate = root.querySelector('#block-row-template');
        const modeButtons = Array.from(root.querySelectorAll('[data-editor-mode]'));

        if (!chapterBuilder || !chapterOutline || !addChapterButton || !chapterTemplate || !blockTemplate) {
            return;
        }

        let editorMode = 'selected';
        let activePageId = '';

        const sanitize = function (value) {
            return String(value || '')
                .replace(/\|/g, '/')
                .replace(/\r?\n/g, ' ')
                .trim();
        };

        const updateBlockDisplay = function (row) {
            if (!row) {
                return;
            }

            const type = row.querySelector('.pt-block-type')?.value || 'text_user';
            const requiredWrap = row.querySelector('.pt-block-required-wrap');
            const adminWrap = row.querySelector('.pt-block-admin-wrap');
            const captionWrap = row.querySelector('.pt-block-caption-wrap');

            if (requiredWrap) {
                requiredWrap.style.display = type === 'text_admin' ? 'none' : '';
            }

            if (adminWrap) {
                adminWrap.style.display = type === 'text_admin' ? '' : 'none';
            }

            if (captionWrap) {
                captionWrap.style.display = type === 'image_user' ? '' : 'none';
            }
        };

        const serializeBlocks = function (chapterRow) {
            const definitionField = chapterRow.querySelector('[data-blocks-definition]');
            if (!definitionField) {
                return;
            }

            const rows = Array.from(chapterRow.querySelectorAll('.pt-block-row'));
            const lines = rows.map(function (row) {
                const type = row.querySelector('.pt-block-type')?.value || 'text_user';
                const prompt = sanitize(row.querySelector('.pt-block-prompt')?.value);
                const isRequired = row.querySelector('.pt-block-required')?.checked ? 'required' : 'optional';

                if (type === 'text_admin') {
                    const adminText = sanitize(row.querySelector('.pt-block-admin')?.value);
                    return 'text_admin|' + prompt + '|' + adminText;
                }

                if (type === 'image_user') {
                    const caption = row.querySelector('.pt-block-caption')?.value === 'above' ? 'above' : 'below';
                    return 'image_user|' + isRequired + '|' + caption + '|' + (prompt || 'Upload an image');
                }

                return 'text_user|' + isRequired + '|' + (prompt || 'Complete this section');
            });

            if (lines.length === 0) {
                lines.push('text_user|required|Complete this section');
            }

            definitionField.value = lines.join('\n');
        };

        const updateBlockIndexing = function (chapterRow) {
            const rows = Array.from(chapterRow.querySelectorAll('.pt-block-row'));
            rows.forEach(function (row, index) {
                row.dataset.blockIndex = String(index);
                const order = row.querySelector('.block-order');
                if (order) {
                    order.textContent = String(index + 1);
                }
            });
        };

        const updateChapterIndexing = function () {
            const rows = Array.from(chapterBuilder.querySelectorAll('.chapter-row'));
            rows.forEach(function (row, index) {
                row.dataset.rowIndex = String(index);
                row.dataset.pageId = 'chapter-page-' + index;

                const order = row.querySelector('.chapter-order');
                if (order) {
                    order.textContent = String(index + 1);
                }

                Array.from(row.querySelectorAll('input[name], textarea[name], select[name]')).forEach(function (field) {
                    field.name = field.name.replace(/chapters\[\d+\]/, 'chapters[' + index + ']');
                });

                updateBlockIndexing(row);
                serializeBlocks(row);
            });

            const removeButtons = Array.from(chapterBuilder.querySelectorAll('[data-remove-row]'));
            removeButtons.forEach(function (button) {
                button.disabled = rows.length <= 1;
            });

            if (!activePageId && rows[0]) {
                activePageId = rows[0].dataset.pageId || '';
            }

            if (activePageId && !rows.some((row) => row.dataset.pageId === activePageId)) {
                activePageId = rows[0] ? rows[0].dataset.pageId || '' : '';
            }
        };

        const buildOutline = function () {
            const rows = Array.from(chapterBuilder.querySelectorAll('.chapter-row'));
            chapterOutline.innerHTML = '';

            const allItem = document.createElement('li');
            allItem.innerHTML = '<button type="button" class="doc-nav-link" data-outline-target="all">All pages</button>';
            chapterOutline.appendChild(allItem);

            rows.forEach(function (row, chapterIndex) {
                const li = document.createElement('li');
                li.className = 'doc-nav-group';

                const titleField = row.querySelector('[data-chapter-title]');
                const chapterTitle = (titleField?.value || '').trim() || 'Chapter ' + (chapterIndex + 1);
                const pageId = row.dataset.pageId || '';

                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'doc-nav-link';
                button.dataset.outlineTarget = pageId;
                button.innerHTML = '<span>' + String(chapterIndex + 1) + '.</span><span>' + chapterTitle + '</span>';
                li.appendChild(button);

                const blockRows = Array.from(row.querySelectorAll('.pt-block-row'));
                if (blockRows.length > 0) {
                    const blockList = document.createElement('ul');
                    blockList.className = 'doc-block-nav';

                    blockRows.forEach(function (blockRow, blockIndex) {
                        const blockLi = document.createElement('li');
                        const blockButton = document.createElement('button');
                        const blockType = blockRow.querySelector('.pt-block-type')?.value || 'text_user';
                        const prompt = (blockRow.querySelector('.pt-block-prompt')?.value || '').trim();
                        const blockTitle = prompt || blockType.replace('_', ' ');

                        blockButton.type = 'button';
                        blockButton.className = 'doc-nav-link doc-nav-link--block';
                        blockButton.dataset.outlineTarget = pageId;
                        blockButton.dataset.outlineBlock = String(blockIndex);
                        blockButton.innerHTML = '<span>' + String(chapterIndex + 1) + '.' + String(blockIndex + 1) + '</span><span>' + blockTitle + '</span>';

                        blockLi.appendChild(blockButton);
                        blockList.appendChild(blockLi);
                    });

                    li.appendChild(blockList);
                }

                chapterOutline.appendChild(li);
            });

            applyPageMode();
        };

        const applyPageMode = function () {
            const rows = Array.from(chapterBuilder.querySelectorAll('.chapter-row'));

            rows.forEach(function (row) {
                const pageId = row.dataset.pageId || '';
                const show = editorMode === 'all' || pageId === activePageId;
                row.style.display = show ? '' : 'none';
            });

            modeButtons.forEach(function (button) {
                button.classList.toggle('is-active', button.dataset.editorMode === editorMode);
            });

            Array.from(chapterOutline.querySelectorAll('[data-outline-target]')).forEach(function (button) {
                const target = button.dataset.outlineTarget;
                const active = editorMode === 'all' ? target === 'all' : target === activePageId;
                button.classList.toggle('is-active', active);
            });
        };

        const createDefaultBlockHtml = function () {
            return blockTemplate.innerHTML
                .replaceAll('__BLOCK_INDEX__', '0')
                .replaceAll('__BLOCK_ORDER__', '1');
        };

        const addChapter = function () {
            const nextIndex = chapterBuilder.querySelectorAll('.chapter-row').length;
            const html = chapterTemplate.innerHTML
                .replaceAll('__INDEX__', String(nextIndex))
                .replaceAll('__INDEX_ONE__', String(nextIndex + 1))
                .replace('__BLOCK_ROW__', createDefaultBlockHtml());

            chapterBuilder.insertAdjacentHTML('beforeend', html);
            const rows = chapterBuilder.querySelectorAll('.chapter-row');
            const created = rows[rows.length - 1];

            if (created) {
                activePageId = created.dataset.pageId || '';
                editorMode = 'selected';
                updateChapterIndexing();
                Array.from(created.querySelectorAll('.pt-block-row')).forEach(updateBlockDisplay);
                buildOutline();
                created.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        };

        root.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const addBlockButton = target.closest('[data-add-block]');
            if (addBlockButton) {
                const chapterRow = addBlockButton.closest('.chapter-row');
                if (!chapterRow) {
                    return;
                }

                const list = chapterRow.querySelector('[data-block-list]');
                if (!list) {
                    return;
                }

                const blockCount = list.querySelectorAll('.pt-block-row').length;
                const html = blockTemplate.innerHTML
                    .replaceAll('__BLOCK_INDEX__', String(blockCount))
                    .replaceAll('__BLOCK_ORDER__', String(blockCount + 1));
                list.insertAdjacentHTML('beforeend', html);
                const added = list.querySelectorAll('.pt-block-row')[blockCount];
                updateBlockDisplay(added);
                updateBlockIndexing(chapterRow);
                serializeBlocks(chapterRow);
                buildOutline();
                return;
            }

            const removeBlockButton = target.closest('[data-remove-block]');
            if (removeBlockButton) {
                const chapterRow = removeBlockButton.closest('.chapter-row');
                const blockRow = removeBlockButton.closest('.pt-block-row');
                if (!chapterRow || !blockRow) {
                    return;
                }

                const currentBlocks = chapterRow.querySelectorAll('.pt-block-row');
                if (currentBlocks.length <= 1) {
                    return;
                }

                blockRow.remove();
                updateBlockIndexing(chapterRow);
                serializeBlocks(chapterRow);
                buildOutline();
                return;
            }

            const removeChapterButton = target.closest('[data-remove-row]');
            if (removeChapterButton) {
                const rows = chapterBuilder.querySelectorAll('.chapter-row');
                if (rows.length <= 1) {
                    return;
                }

                const chapterRow = removeChapterButton.closest('.chapter-row');
                const removedPageId = chapterRow?.dataset.pageId || '';
                chapterRow?.remove();

                updateChapterIndexing();
                if (activePageId === removedPageId) {
                    const first = chapterBuilder.querySelector('.chapter-row');
                    activePageId = first ? first.dataset.pageId || '' : '';
                }
                buildOutline();
                return;
            }

            const outlineButton = target.closest('[data-outline-target]');
            if (outlineButton) {
                const outlineTarget = outlineButton.getAttribute('data-outline-target') || '';

                if (outlineTarget === 'all') {
                    editorMode = 'all';
                    applyPageMode();
                    return;
                }

                activePageId = outlineTarget;
                editorMode = 'selected';
                applyPageMode();

                const page = chapterBuilder.querySelector('[data-page-id="' + outlineTarget + '"]');
                const blockIndex = outlineButton.getAttribute('data-outline-block');

                window.requestAnimationFrame(function () {
                    if (!page) {
                        return;
                    }

                    if (blockIndex !== null) {
                        const block = page.querySelector('.pt-block-row[data-block-index="' + blockIndex + '"]');
                        block?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    page.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        });

        root.addEventListener('input', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const chapterRow = target.closest('.chapter-row');
            if (!chapterRow) {
                return;
            }

            if (target.classList.contains('pt-block-prompt') || target.hasAttribute('data-chapter-title') || target.classList.contains('pt-block-admin')) {
                buildOutline();
            }

            if (target.classList.contains('pt-block-prompt') || target.classList.contains('pt-block-admin')) {
                serializeBlocks(chapterRow);
            }
        });

        root.addEventListener('change', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const chapterRow = target.closest('.chapter-row');
            if (!chapterRow) {
                return;
            }

            if (target.classList.contains('pt-block-type')) {
                updateBlockDisplay(target.closest('.pt-block-row'));
                updateBlockIndexing(chapterRow);
                serializeBlocks(chapterRow);
                buildOutline();
                return;
            }

            if (target.classList.contains('pt-block-required') || target.classList.contains('pt-block-caption')) {
                serializeBlocks(chapterRow);
                buildOutline();
            }
        });

        modeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                editorMode = button.dataset.editorMode === 'all' ? 'all' : 'selected';
                applyPageMode();
            });
        });

        addChapterButton.addEventListener('click', addChapter);

        updateChapterIndexing();
        Array.from(root.querySelectorAll('.pt-block-row')).forEach(updateBlockDisplay);
        buildOutline();
    })();
</script>
