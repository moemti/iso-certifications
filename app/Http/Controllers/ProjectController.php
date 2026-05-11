<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function create()
    {
        $projectTypes = ProjectType::with(['templateChapters' => fn ($query) => $query->orderBy('sort_order')])
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

        DB::transaction(function () use ($validated, $request, $projectType): void {
            $project = Project::create([
                ...$validated,
                'is_active' => $request->boolean('is_active'),
                'created_by_user_id' => $request->user()->id,
            ]);

            foreach ($projectType->templateChapters as $chapter) {
                $project->chapters()->create([
                    'project_type_chapter_id' => $chapter->id,
                    'title' => $chapter->title,
                    'description' => $chapter->description,
                    'sort_order' => $chapter->sort_order,
                    'status' => 'partial_completed',
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Project created successfully.');
    }
}
