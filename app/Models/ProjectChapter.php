<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChapter extends Model
{
    protected $fillable = [
        'project_id',
        'project_type_chapter_id',
        'title',
        'description',
        'admin_default_text',
        'user_content',
        'sort_order',
        'is_required',
        'is_user_editable',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_user_editable' => 'boolean',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function template()
    {
        return $this->belongsTo(ProjectTypeChapter::class, 'project_type_chapter_id');
    }

    public function blocks()
    {
        return $this->hasMany(ProjectChapterBlock::class, 'project_chapter_id')->orderBy('sort_order');
    }
}
