<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'project_type_id',
        'created_by_user_id',
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

    public static function rules(): array
    {
        return [
            'project_type_id' => 'required|exists:project_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function chapters()
    {
        return $this->hasMany(ProjectChapter::class)->orderBy('sort_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
