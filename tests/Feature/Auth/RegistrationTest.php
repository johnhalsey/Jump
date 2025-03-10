<?php

namespace Tests\Feature\Auth;

use App\Models\Project;
use App\Models\Invitation;
use App\Events\UserAddedToProject;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_project_invite_can_be_auto_accepted()
    {
        $project = Project::factory()->create();
        $invite = Invitation::factory()->create([
            'project_id' => $project->id,
            'email' => 'test@example.com',
        ]);

        Event::fake();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $invite->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'project_id' => $project->id,
            'auto_accepted' => true,
        ]);

        $this->assertAuthenticated();

        $this->assertCount(1, $project->users);
        $this->assertCount(0, Invitation::all());
        $response->assertRedirect(route('dashboard', absolute: false));
        Event::assertDispatched(UserAddedToProject::class);

    }
}
