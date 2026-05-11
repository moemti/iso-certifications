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
        $chapters = $this->parseChapters($request->input('chapters'));

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
                $projectType->templateChapters()->create([
                    'title' => $chapter,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('project-types.index')->with('success', 'Project type added successfully.');
    }

    public function edit(ProjectType $projectType)
    {
        $projectType->load('templateChapters');

        return view('project-types.edit', compact('projectType'));
    }

    public function update(Request $request, ProjectType $projectType)
    {
        $validated = $request->validate(ProjectType::rules($projectType->id));
        $chapters = $this->parseChapters($request->input('chapters'));

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
                $projectType->templateChapters()->create([
                    'title' => $chapter,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
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

    private function parseChapters(?string $chapters): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $chapters))
            ->map(static fn (string $line): string => trim($line))
            ->filter(static fn (string $line): bool => $line !== '')
            ->values()
            ->all();
    }
}
