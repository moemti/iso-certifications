<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChapterBlock extends Model
{
    protected $fillable = [
        'project_chapter_id',
        'project_type_chapter_block_id',
        'block_type',
        'editable_by',
        'prompt_text',
        'admin_content',
        'user_text',
        'image_path',
        'caption_position',
        'sort_order',
        'is_required',
        'is_active',
        'status',
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
        return $this->belongsTo(ProjectChapter::class, 'project_chapter_id');
    }

    public function templateBlock()
    {
        return $this->belongsTo(ProjectTypeChapterBlock::class, 'project_type_chapter_block_id');
    }
}
