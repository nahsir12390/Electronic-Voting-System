<?php

use App\Models\Election;
use App\Models\User;

function voterAdmin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

test('an admin can create a verified voter account', function () {
    $admin = voterAdmin();

    $this->actingAs($admin)
        ->post(route('admin.voters.store'), [
            'name' => 'Mary Musa',
            'email' => 'mary@example.com',
            'password' => 'securepass123',
            'password_confirmation' => 'securepass123',
        ])
        ->assertSessionHas('status', 'Voter account created.');

    $voter = User::where('email', 'mary@example.com')->first();

    expect($voter)->not->toBeNull();
    expect($voter->is_admin)->toBeFalse();
    expect($voter->email_verified_at)->not->toBeNull();

    $this->actingAs($voter)
        ->get(route('dashboard'))
        ->assertOk();
});

test('an admin can view the voter management page', function () {
    $admin = voterAdmin();
    $voter = User::factory()->create([
        'name' => 'Ready Voter',
        'email' => 'ready@example.com',
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.voters.index'))
        ->assertOk()
        ->assertSee('Voter account management')
        ->assertSee('Ready Voter')
        ->assertSee('ready@example.com');
});

test('an admin can delete a voter without recorded votes', function () {
    $admin = voterAdmin();
    $voter = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.voters.destroy', $voter))
        ->assertSessionHas('status', 'Voter account deleted.');

    $this->assertDatabaseMissing('users', ['id' => $voter->id]);
});

test('an admin cannot delete a voter with recorded votes', function () {
    $admin = voterAdmin();
    $voter = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);
    $election = Election::create([
        'title' => 'Protected Voter Election',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $candidate = $election->candidates()->create(['name' => 'Protected Candidate']);

    $this->actingAs($voter)
        ->post(route('votes.store', $election), ['candidate_id' => $candidate->id]);

    $this->actingAs($admin)
        ->delete(route('admin.voters.destroy', $voter))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('users', ['id' => $voter->id]);
});