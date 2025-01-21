<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded=[];

    protected $casts = [
        'supported_documents' => 'array',
        'assign_to'=>'array',
    ];

}
