<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function rules($id = null): array
    {
        $nameRule = 'required|string|max:255|unique:project_types,name';

        if ($id) {
            $nameRule .= ',' . $id;
        }

        return [
            'name' => $nameRule,
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function templateChapters()
    {
        return $this->hasMany(ProjectTypeChapter::class)->orderBy('sort_order');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
