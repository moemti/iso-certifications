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
        'sort_order',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function template()
    {
        return $this->belongsTo(ProjectTypeChapter::class, 'project_type_chapter_id');
    }
}
