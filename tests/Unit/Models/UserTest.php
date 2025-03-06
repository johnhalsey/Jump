<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_hasAFullProfile()
    {
        $user = User::factory()->create([
            'first_name' => null,
            'last_name' => null,
        ]);

        $this->assertFalse($user->hasFullProfile());

        $user = User::factory()->create([
            'first_name' => null,
            'last_name' => 'smith',
        ]);

        $this->assertFalse($user->hasFullProfile());

        $user = User::factory()->create([
            'first_name' => 'john',
            'last_name' => null,
        ]);

        $this->assertFalse($user->hasFullProfile());

        $user = User::factory()->create([
            'first_name' => 'john',
            'last_name' => 'smith',
        ]);

        $this->assertTrue($user->hasFullProfile());
    }
}
