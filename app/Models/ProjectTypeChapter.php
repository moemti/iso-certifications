<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeChapter extends Model
{
    protected $fillable = [
        'project_type_id',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }
}
