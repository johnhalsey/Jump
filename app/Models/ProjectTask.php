<?php

namespace App\Models;

use App\Enums\ProjectPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TaskNote::class, 'task_id')->orderByDesc('created_at');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Link::class, 'linkable');
    }
}
