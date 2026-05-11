<x-layouts.app :title="$project->name">
    <div class="doc-page-shell">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="doc-layout">
            <aside class="doc-sidebar" aria-label="Project Chapters">
                <h2 class="doc-sidebar-title">{{ $project->name }}</h2>
                <p class="doc-sidebar-subtitle">{{ $project->projectType->name }}</p>

                <ul class="doc-chapter-nav">
                    @foreach ($project->chapters as $chapter)
                        <li>
                            <a href="#chapter-{{ $chapter->id }}" class="doc-nav-link">
                                <span>{{ $chapter->sort_order }}.</span>
                                <span>{{ $chapter->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <section class="doc-main" aria-label="Document Content">
                <div class="doc-paper">
                    <header class="doc-header">
                        <h1>{{ $project->name }}</h1>
                        <p>Template: {{ $project->projectType->name }}</p>
                    </header>

                    @foreach ($project->chapters as $chapter)
                        <article class="doc-chapter" id="chapter-{{ $chapter->id }}">
                            <div class="doc-chapter-head">
                                <h2>{{ $chapter->sort_order }}. {{ $chapter->title }}</h2>
                                <div class="doc-tags">
                                    <span class="doc-tag {{ $chapter->is_required ? 'doc-tag-required' : '' }}">
                                        {{ $chapter->is_required ? 'Required' : 'Optional' }}
                                    </span>
                                    <span class="doc-tag {{ $chapter->is_user_editable ? 'doc-tag-editable' : 'doc-tag-locked' }}">
                                        {{ $chapter->is_user_editable ? 'Editable by user' : 'Read only' }}
                                    </span>
                                    <span class="doc-tag">
                                        {{ $chapter->status === 'completed' ? 'Completed' : 'Partial' }}
                                    </span>
                                </div>
                            </div>

                            @if (!empty($chapter->description))
                                <p class="doc-guidance">{{ $chapter->description }}</p>
                            @endif

                            @if ($chapter->blocks->isEmpty() && !empty($chapter->admin_default_text))
                                <div class="doc-template-text">{!! nl2br(e($chapter->admin_default_text)) !!}</div>
                            @endif

                            @foreach ($chapter->blocks as $block)
                                @php
                                    $canEdit = auth()->user()->IsAdmin
                                        ? $block->editable_by !== 'user'
                                        : in_array($block->editable_by, ['user', 'both'], true);
                                @endphp

                                <section class="doc-block">
                                    <div class="doc-block-head">
                                        <span class="doc-tag">{{ strtoupper($block->block_type) }}</span>
                                        @if ($block->is_required)
                                            <span class="doc-tag doc-tag-required">Required</span>
                                        @endif
                                        <span class="doc-tag">{{ ucfirst($block->editable_by) }} edit</span>
                                    </div>

                                    @if (!empty($block->prompt_text))
                                        <p class="doc-guidance">{{ $block->prompt_text }}</p>
                                    @endif

                                    @if (!empty($block->admin_content))
                                        <div class="doc-template-text">{!! nl2br(e($block->admin_content)) !!}</div>
                                    @endif

                                    @if ($block->block_type === 'image' && !empty($block->image_path))
                                        @if (($block->caption_position ?? 'below') === 'above' && !empty($block->user_text))
                                            <p class="doc-image-caption">{{ $block->user_text }}</p>
                                        @endif

                                        <img src="{{ asset('storage/' . $block->image_path) }}" alt="Chapter image" class="doc-image-preview">

                                        @if (($block->caption_position ?? 'below') === 'below' && !empty($block->user_text))
                                            <p class="doc-image-caption">{{ $block->user_text }}</p>
                                        @endif
                                    @endif

                                    @if ($canEdit)
                                        <form method="POST" action="{{ route('projects.chapters.blocks.update', [$project, $chapter, $block]) }}" class="doc-edit-form" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')

                                            @if ($block->block_type === 'text')
                                                <label class="form-label" for="block_text_{{ $block->id }}">Your Paragraph{{ $block->is_required ? ' *' : '' }}</label>
                                                <textarea
                                                    class="form-input"
                                                    id="block_text_{{ $block->id }}"
                                                    name="user_text"
                                                    rows="6"
                                                    placeholder="Write your paragraph..."
                                                >{{ old('user_text', $block->user_text) }}</textarea>
                                            @else
                                                <label class="form-label" for="block_image_{{ $block->id }}">Upload image{{ $block->is_required ? ' *' : '' }}</label>
                                                <input class="form-input" id="block_image_{{ $block->id }}" type="file" name="image" accept="image/*">

                                                <label class="form-label" for="block_caption_{{ $block->id }}">Image text</label>
                                                <textarea class="form-input" id="block_caption_{{ $block->id }}" name="user_text" rows="3" placeholder="Caption or explanation for this image">{{ old('user_text', $block->user_text) }}</textarea>

                                                <label class="form-label" for="block_caption_position_{{ $block->id }}">Caption position</label>
                                                <select class="form-input" id="block_caption_position_{{ $block->id }}" name="caption_position">
                                                    <option value="above" {{ ($block->caption_position ?? 'below') === 'above' ? 'selected' : '' }}>Above image</option>
                                                    <option value="below" {{ ($block->caption_position ?? 'below') === 'below' ? 'selected' : '' }}>Below image</option>
                                                </select>
                                            @endif

                                            <div class="doc-form-actions">
                                                <label class="form-check" style="margin-top: 0;">
                                                    <input type="radio" name="status" value="partial_completed" {{ $block->status !== 'completed' ? 'checked' : '' }}>
                                                    Partial
                                                </label>
                                                <label class="form-check" style="margin-top: 0;">
                                                    <input type="radio" name="status" value="completed" {{ $block->status === 'completed' ? 'checked' : '' }}>
                                                    Completed
                                                </label>
                                                <button type="submit" class="button button-primary">Save Block</button>
                                            </div>
                                        </form>
                                    @else
                                        <p class="doc-locked-note">This block is not editable in your role.</p>
                                    @endif
                                </section>
                            @endforeach
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
