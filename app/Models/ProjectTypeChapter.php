<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeChapter extends Model
{
    protected $fillable = [
        'project_type_id',
        'title',
        'description',
        'admin_default_text',
        'sort_order',
        'is_required',
        'is_user_editable',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_user_editable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function blocks()
    {
        return $this->hasMany(ProjectTypeChapterBlock::class, 'project_type_chapter_id')->orderBy('sort_order');
    }
}
