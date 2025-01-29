<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $guarded=[];

    protected $casts = [
        'supported_documents' => 'array',
        'assign_to'=>'array',
    ];


    public function projectSprints(): HasMany
    {
        return $this->hasMany(ProjectSprint::class);
    }

}
