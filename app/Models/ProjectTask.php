<?php

namespace App\Models;

use Laravel\Prompts\Note;
use App\Observers\ProjectTaskObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function status()
    {
        return $this->project->statuses()->where('id', $this->status_id)->first();
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaskNote::class, 'task_id');
    }
}
