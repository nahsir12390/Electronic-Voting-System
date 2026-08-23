<?php

use App\Models\Election;
use App\Models\User;

test('an authenticated voter can cast one vote in an open election', function () {
    $user = User::factory()->create();
    $election = Election::create([
        'title' => 'Class Representative',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $candidate = $election->candidates()->create(['name' => 'Jane Candidate']);

    $response = $this
        ->actingAs($user)
        ->post(route('votes.store', $election), ['candidate_id' => $candidate->id]);

    $response->assertRedirect();
    $this->assertDatabaseHas('votes', [
        'election_id' => $election->id,
        'candidate_id' => $candidate->id,
        'user_id' => $user->id,
    ]);
});

test('a voter cannot vote twice in the same election', function () {
    $user = User::factory()->create();
    $election = Election::create([
        'title' => 'Department Chair',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $firstCandidate = $election->candidates()->create(['name' => 'First Candidate']);
    $secondCandidate = $election->candidates()->create(['name' => 'Second Candidate']);

    $this->actingAs($user)
        ->post(route('votes.store', $election), ['candidate_id' => $firstCandidate->id]);

    $this->actingAs($user)
        ->post(route('votes.store', $election), ['candidate_id' => $secondCandidate->id])
        ->assertSessionHas('status', 'You have already voted in this election.');

    expect($election->votes()->count())->toBe(1);
});

test('a voter cannot submit a candidate from another election', function () {
    $user = User::factory()->create();
    $election = Election::create([
        'title' => 'Treasurer',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $otherElection = Election::create([
        'title' => 'Secretary',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $candidate = $otherElection->candidates()->create(['name' => 'Wrong Ballot']);

    $this->actingAs($user)
        ->post(route('votes.store', $election), ['candidate_id' => $candidate->id])
        ->assertNotFound();

    expect($election->votes()->count())->toBe(0);
});
