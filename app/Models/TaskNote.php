<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskNote extends Model
{
    use HasFactory;

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }
}
