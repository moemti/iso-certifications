<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeChapterBlock extends Model
{
    protected $fillable = [
        'project_type_chapter_id',
        'block_type',
        'editable_by',
        'prompt_text',
        'admin_content',
        'caption_position',
        'sort_order',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function chapter()
    {
        return $this->belongsTo(ProjectTypeChapter::class, 'project_type_chapter_id');
    }
}
