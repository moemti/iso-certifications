<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectChapter;
use App\Models\ProjectChapterBlock;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function create()
    {
        $projectTypes = ProjectType::with(['templateChapters' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('projectTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_type_id' => [
                'required',
                Rule::exists('project_types', 'id')->where('is_active', true),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        $projectType = ProjectType::with(['templateChapters' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->findOrFail($validated['project_type_id']);

        if ($projectType->templateChapters->isEmpty()) {
            return back()
                ->withErrors(['project_type_id' => 'Selected project type does not have any active template chapters.'])
                ->withInput();
        }

        $project = DB::transaction(function () use ($validated, $request, $projectType): Project {
            $createdProject = Project::create([
                ...$validated,
                'is_active' => $request->boolean('is_active'),
                'created_by_user_id' => $request->user()->id,
            ]);

            foreach ($projectType->templateChapters as $chapter) {
                $createdChapter = $createdProject->chapters()->create([
                    'project_type_chapter_id' => $chapter->id,
                    'title' => $chapter->title,
                    'description' => $chapter->description,
                    'admin_default_text' => $chapter->admin_default_text,
                    'sort_order' => $chapter->sort_order,
                    'is_required' => $chapter->is_required,
                    'is_user_editable' => $chapter->is_user_editable,
                    'status' => 'partial_completed',
                ]);

                $templateBlocks = $chapter->blocks()->where('is_active', true)->orderBy('sort_order')->get();

                foreach ($templateBlocks as $templateBlock) {
                    $createdChapter->blocks()->create([
                        'project_type_chapter_block_id' => $templateBlock->id,
                        'block_type' => $templateBlock->block_type,
                        'editable_by' => $templateBlock->editable_by,
                        'prompt_text' => $templateBlock->prompt_text,
                        'admin_content' => $templateBlock->admin_content,
                        'caption_position' => $templateBlock->caption_position,
                        'sort_order' => $templateBlock->sort_order,
                        'is_required' => $templateBlock->is_required,
                        'is_active' => $templateBlock->is_active,
                        'status' => 'partial_completed',
                    ]);
                }
            }

            return $createdProject;
        });

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->ensureProjectAccess($project);

        $project->load(['projectType', 'chapters.blocks']);

        return view('projects.show', compact('project'));
    }

    public function updateChapter(Request $request, Project $project, ProjectChapter $projectChapter)
    {
        $this->ensureProjectAccess($project);

        if ($projectChapter->project_id !== $project->id) {
            abort(404);
        }

        if (!$projectChapter->is_user_editable) {
            return back()->with('error', 'This chapter is read-only and can only be changed by template administrators.');
        }

        $validated = $request->validate([
            'user_content' => $projectChapter->is_required
                ? 'required|string|max:20000'
                : 'nullable|string|max:20000',
            'status' => ['required', Rule::in(['partial_completed', 'completed'])],
        ]);

        $projectChapter->update([
            'user_content' => trim((string) $validated['user_content']) ?: null,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Chapter updated successfully.');
    }

    public function updateBlock(Request $request, Project $project, ProjectChapter $projectChapter, ProjectChapterBlock $projectChapterBlock)
    {
        $this->ensureProjectAccess($project);

        if ($projectChapter->project_id !== $project->id || $projectChapterBlock->project_chapter_id !== $projectChapter->id) {
            abort(404);
        }

        $isAdmin = (bool) auth()->user()->IsAdmin;
        $editableBy = $projectChapterBlock->editable_by;

        if (!$isAdmin && !in_array($editableBy, ['user', 'both'], true)) {
            return back()->with('error', 'This paragraph is admin-managed and cannot be edited by user.');
        }

        if ($isAdmin && $editableBy === 'user') {
            return back()->with('error', 'This paragraph is user-managed.');
        }

        $rules = [
            'status' => ['required', Rule::in(['partial_completed', 'completed'])],
            'user_text' => $projectChapterBlock->is_required && $projectChapterBlock->block_type === 'text'
                ? 'required|string|max:20000'
                : 'nullable|string|max:20000',
            'image' => $projectChapterBlock->block_type === 'image'
                ? ($projectChapterBlock->is_required && empty($projectChapterBlock->image_path)
                    ? 'required|image|max:8192'
                    : 'nullable|image|max:8192')
                : 'nullable',
            'caption_position' => ['nullable', Rule::in(['above', 'below'])],
        ];

        $validated = $request->validate($rules);

        $newImagePath = $projectChapterBlock->image_path;

        if ($request->hasFile('image') && $projectChapterBlock->block_type === 'image') {
            if (!empty($projectChapterBlock->image_path)) {
                Storage::disk('public')->delete($projectChapterBlock->image_path);
            }

            $newImagePath = $request->file('image')->store('project-chapters', 'public');
        }

        $projectChapterBlock->update([
            'user_text' => trim((string) ($validated['user_text'] ?? '')) ?: null,
            'image_path' => $newImagePath,
            'caption_position' => $validated['caption_position'] ?? $projectChapterBlock->caption_position,
            'status' => $validated['status'],
        ]);

        $this->recalculateChapterStatus($projectChapter);

        return back()->with('success', 'Block updated successfully.');
    }

    private function ensureProjectAccess(Project $project): void
    {
        $user = auth()->user();

        if ($user->IsAdmin) {
            return;
        }

        if ($project->created_by_user_id !== $user->id) {
            abort(403);
        }
    }

    private function recalculateChapterStatus(ProjectChapter $projectChapter): void
    {
        $requiredBlocks = $projectChapter->blocks()->where('is_required', true)->get();

        if ($requiredBlocks->isEmpty()) {
            $projectChapter->update(['status' => 'completed']);
            return;
        }

        $allCompleted = $requiredBlocks->every(function (ProjectChapterBlock $block): bool {
            if ($block->status !== 'completed') {
                return false;
            }

            if ($block->block_type === 'image') {
                return !empty($block->image_path);
            }

            return !empty($block->user_text) || !empty($block->admin_content);
        });

        $projectChapter->update([
            'status' => $allCompleted ? 'completed' : 'partial_completed',
        ]);
    }
}
