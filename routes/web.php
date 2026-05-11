<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTypeController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    $projectsQuery = Project::with('projectType')->withCount('chapters')->latest();

    if (!$user->IsAdmin) {
        $projectsQuery->where('created_by_user_id', $user->id);
    }

    $projects = $projectsQuery->get();

    return view('dashboard', compact('projects'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::get('/partners/create', [PartnerController::class, 'create'])->name('partners.create');
    Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
    Route::get('/partners/{partner}/edit', [PartnerController::class, 'edit'])->name('partners.edit');
    Route::patch('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
    Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');

    Route::get('/project-types', [ProjectTypeController::class, 'index'])->name('project-types.index');
    Route::get('/project-types/create', [ProjectTypeController::class, 'create'])->name('project-types.create');
    Route::post('/project-types', [ProjectTypeController::class, 'store'])->name('project-types.store');
    Route::get('/project-types/{projectType}/edit', [ProjectTypeController::class, 'edit'])->name('project-types.edit');
    Route::patch('/project-types/{projectType}', [ProjectTypeController::class, 'update'])->name('project-types.update');
    Route::delete('/project-types/{projectType}', [ProjectTypeController::class, 'destroy'])->name('project-types.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}/chapters/{projectChapter}', [ProjectController::class, 'updateChapter'])->name('projects.chapters.update');
    Route::patch('/projects/{project}/chapters/{projectChapter}/blocks/{projectChapterBlock}', [ProjectController::class, 'updateBlock'])->name('projects.chapters.blocks.update');
});

require __DIR__.'/auth.php';
