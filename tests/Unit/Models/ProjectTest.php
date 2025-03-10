<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_userAddedWithinLastHour()
    {
        Carbon::setTestNow(now());

        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->users()->attach($user, ['created_at' => now()->subHours(2)]);

        $this->assertFalse($project->userAddedWithinLastHour($user));

        $project = Project::factory()->create();
        $user = User::factory()->create();
        $project->users()->attach($user, ['created_at' => now()->subMinutes(59)]);

        $this->assertTrue($project->userAddedWithinLastHour($user));
    }
}
