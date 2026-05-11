<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    public function index()
    {
        $projectTypes = ProjectType::withCount('templateChapters')->paginate(15);

        return view('project-types.index', compact('projectTypes'));
    }

    public function create()
    {
        return view('project-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(ProjectType::rules());
        $chapters = $this->validateAndNormalizeChapters($request);

        if (count($chapters) === 0) {
            return back()
                ->withErrors(['chapters' => 'At least one chapter is required for the template.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $request, $chapters): void {
            $projectType = ProjectType::create([
                ...$validated,
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach ($chapters as $index => $chapter) {
                $createdChapter = $projectType->templateChapters()->create([
                    'title' => $chapter['title'],
                    'description' => $chapter['description'],
                    'admin_default_text' => $chapter['admin_default_text'],
                    'sort_order' => $index + 1,
                    'is_required' => $chapter['is_required'],
                    'is_user_editable' => $chapter['is_user_editable'],
                    'is_active' => $chapter['is_active'],
                ]);

                foreach ($chapter['blocks'] as $blockIndex => $block) {
                    $createdChapter->blocks()->create([
                        'block_type' => $block['block_type'],
                        'editable_by' => $block['editable_by'],
                        'prompt_text' => $block['prompt_text'],
                        'admin_content' => $block['admin_content'],
                        'caption_position' => $block['caption_position'],
                        'sort_order' => $blockIndex + 1,
                        'is_required' => $block['is_required'],
                        'is_active' => $block['is_active'],
                    ]);
                }
            }
        });

        return redirect()->route('project-types.index')->with('success', 'Project type added successfully.');
    }

    public function edit(ProjectType $projectType)
    {
        $projectType->load('templateChapters.blocks');

        return view('project-types.edit', compact('projectType'));
    }

    public function update(Request $request, ProjectType $projectType)
    {
        $validated = $request->validate(ProjectType::rules($projectType->id));
        $chapters = $this->validateAndNormalizeChapters($request);

        if (count($chapters) === 0) {
            return back()
                ->withErrors(['chapters' => 'At least one chapter is required for the template.'])
                ->withInput();
        }

        DB::transaction(function () use ($projectType, $validated, $request, $chapters): void {
            $projectType->update([
                ...$validated,
                'is_active' => $request->boolean('is_active'),
            ]);

            $projectType->templateChapters()->delete();

            foreach ($chapters as $index => $chapter) {
                $createdChapter = $projectType->templateChapters()->create([
                    'title' => $chapter['title'],
                    'description' => $chapter['description'],
                    'admin_default_text' => $chapter['admin_default_text'],
                    'sort_order' => $index + 1,
                    'is_required' => $chapter['is_required'],
                    'is_user_editable' => $chapter['is_user_editable'],
                    'is_active' => $chapter['is_active'],
                ]);

                foreach ($chapter['blocks'] as $blockIndex => $block) {
                    $createdChapter->blocks()->create([
                        'block_type' => $block['block_type'],
                        'editable_by' => $block['editable_by'],
                        'prompt_text' => $block['prompt_text'],
                        'admin_content' => $block['admin_content'],
                        'caption_position' => $block['caption_position'],
                        'sort_order' => $blockIndex + 1,
                        'is_required' => $block['is_required'],
                        'is_active' => $block['is_active'],
                    ]);
                }
            }
        });

        return redirect()->route('project-types.index')->with('success', 'Project type updated successfully.');
    }

    public function destroy(ProjectType $projectType)
    {
        if ($projectType->projects()->exists()) {
            return redirect()
                ->route('project-types.index')
                ->with('error', 'Cannot delete this project type because projects already use it.');
        }

        $projectType->delete();

        return redirect()->route('project-types.index')->with('success', 'Project type deleted successfully.');
    }

    private function validateAndNormalizeChapters(Request $request): array
    {
        $request->validate([
            'chapters' => 'required|array|min:1',
            'chapters.*.title' => 'required|string|max:255',
            'chapters.*.description' => 'nullable|string|max:2000',
            'chapters.*.admin_default_text' => 'nullable|string|max:10000',
            'chapters.*.is_required' => 'nullable|boolean',
            'chapters.*.is_user_editable' => 'nullable|boolean',
            'chapters.*.is_active' => 'nullable|boolean',
            'chapters.*.blocks_definition' => 'nullable|string|max:30000',
        ]);

        return collect($request->input('chapters', []))
            ->map(function (array $chapter): array {
                $toBoolean = static fn ($value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;

                return [
                    'title' => trim((string) ($chapter['title'] ?? '')),
                    'description' => trim((string) ($chapter['description'] ?? '')) ?: null,
                    'admin_default_text' => trim((string) ($chapter['admin_default_text'] ?? '')) ?: null,
                    'is_required' => $toBoolean($chapter['is_required'] ?? false),
                    'is_user_editable' => $toBoolean($chapter['is_user_editable'] ?? false),
                    'is_active' => $toBoolean($chapter['is_active'] ?? true),
                    'blocks' => $this->parseBlocksDefinition((string) ($chapter['blocks_definition'] ?? '')),
                ];
            })
            ->filter(static fn (array $chapter): bool => $chapter['title'] !== '')
            ->values()
            ->all();
    }

    private function parseBlocksDefinition(string $definition): array
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', $definition))
            ->map(static fn (string $line): string => trim($line))
            ->filter(static fn (string $line): bool => $line !== '' && !str_starts_with($line, '#'))
            ->values();

        $blocks = $lines->map(static function (string $line): array {
            $parts = array_map('trim', explode('|', $line));
            $type = strtolower($parts[0] ?? 'text_user');

            return match ($type) {
                'text_admin' => [
                    'block_type' => 'text',
                    'editable_by' => 'admin',
                    'is_required' => false,
                    'caption_position' => null,
                    'prompt_text' => $parts[1] ?? null,
                    'admin_content' => $parts[2] ?? ($parts[1] ?? null),
                    'is_active' => true,
                ],
                'image_user' => [
                    'block_type' => 'image',
                    'editable_by' => 'user',
                    'is_required' => strtolower($parts[1] ?? '') === 'required',
                    'caption_position' => in_array(strtolower($parts[2] ?? ''), ['above', 'below'], true) ? strtolower($parts[2]) : 'below',
                    'prompt_text' => $parts[3] ?? ($parts[2] ?? 'Upload an image'),
                    'admin_content' => null,
                    'is_active' => true,
                ],
                default => [
                    'block_type' => 'text',
                    'editable_by' => 'user',
                    'is_required' => strtolower($parts[1] ?? '') === 'required',
                    'caption_position' => null,
                    'prompt_text' => $parts[2] ?? ($parts[1] ?? $line),
                    'admin_content' => null,
                    'is_active' => true,
                ],
            };
        })->all();

        if (count($blocks) > 0) {
            return $blocks;
        }

        return [[
            'block_type' => 'text',
            'editable_by' => 'user',
            'is_required' => true,
            'caption_position' => null,
            'prompt_text' => 'Complete this section.',
            'admin_content' => null,
            'is_active' => true,
        ]];
    }
}
