<?php

use App\Models\Election;
use App\Models\User;

function adminUser(): User
{
    return User::factory()->create(['is_admin' => true]);
}

test('an admin can update an election', function () {
    $admin = adminUser();
    $election = Election::create([
        'title' => 'Old Title',
        'is_active' => true,
        'show_results' => true,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.elections.update', $election), [
            'title' => 'Updated Election',
            'description' => 'Updated description',
            'starts_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'is_active' => '1',
        ])
        ->assertSessionHas('status', 'Election updated.');

    $this->assertDatabaseHas('elections', [
        'id' => $election->id,
        'title' => 'Updated Election',
        'description' => 'Updated description',
        'is_active' => true,
        'show_results' => false,
    ]);
});

test('an admin can view election results', function () {
    $admin = adminUser();
    $voter = User::factory()->create([
        'name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
    ]);
    $election = Election::create([
        'title' => 'Results Election',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $candidate = $election->candidates()->create(['name' => 'Result Candidate']);

    $this->actingAs($voter)
        ->post(route('votes.store', $election), ['candidate_id' => $candidate->id]);

    $this->actingAs($admin)
        ->get(route('admin.elections.show', $election))
        ->assertOk()
        ->assertSee('Results Election')
        ->assertSee('Result Candidate')
        ->assertSee('Voter ledger')
        ->assertSee('Amina Yusuf')
        ->assertSee('amina@example.com')
        ->assertSee('100%');
});

test('an admin can filter the voter ledger by voter and candidate', function () {
    $admin = adminUser();
    $firstVoter = User::factory()->create([
        'name' => 'Amina Yusuf',
        'email' => 'amina@example.com',
    ]);
    $secondVoter = User::factory()->create([
        'name' => 'Daniel Okafor',
        'email' => 'daniel@example.com',
    ]);
    $election = Election::create([
        'title' => 'Ledger Filter Election',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $firstCandidate = $election->candidates()->create(['name' => 'Amina Candidate']);
    $secondCandidate = $election->candidates()->create(['name' => 'Daniel Candidate']);

    $this->actingAs($firstVoter)
        ->post(route('votes.store', $election), ['candidate_id' => $firstCandidate->id]);

    $this->actingAs($secondVoter)
        ->post(route('votes.store', $election), ['candidate_id' => $secondCandidate->id]);

    $this->actingAs($admin)
        ->get(route('admin.elections.show', [
            'election' => $election,
            'search' => 'amina',
            'candidate' => $firstCandidate->id,
        ]))
        ->assertOk()
        ->assertSee('Filter by voter name or email')
        ->assertSee('1 voters recorded')
        ->assertSee('Amina Yusuf')
        ->assertSee('amina@example.com')
        ->assertDontSee('daniel@example.com');
});

test('an admin can update and delete a candidate without votes', function () {
    $admin = adminUser();
    $election = Election::create(['title' => 'Candidate Admin']);
    $candidate = $election->candidates()->create(['name' => 'Old Candidate']);

    $this->actingAs($admin)
        ->patch(route('admin.candidates.update', $candidate), [
            'name' => 'New Candidate',
            'position' => 'Secretary',
            'manifesto' => 'Clear records.',
        ])
        ->assertSessionHas('status', 'Candidate updated.');

    $this->assertDatabaseHas('candidates', [
        'id' => $candidate->id,
        'name' => 'New Candidate',
        'position' => 'Secretary',
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.candidates.destroy', $candidate))
        ->assertSessionHas('status', 'Candidate deleted.');

    $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);
});

test('an admin cannot delete a candidate with recorded votes', function () {
    $admin = adminUser();
    $voter = User::factory()->create();
    $election = Election::create([
        'title' => 'Protected Candidate',
        'starts_at' => now()->subMinute(),
        'ends_at' => now()->addDay(),
        'is_active' => true,
    ]);
    $candidate = $election->candidates()->create(['name' => 'Voted Candidate']);

    $this->actingAs($voter)
        ->post(route('votes.store', $election), ['candidate_id' => $candidate->id]);

    $this->actingAs($admin)
        ->delete(route('admin.candidates.destroy', $candidate))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas('candidates', ['id' => $candidate->id]);
});

test('an admin can delete an election', function () {
    $admin = adminUser();
    $election = Election::create(['title' => 'Delete Me']);

    $this->actingAs($admin)
        ->delete(route('admin.elections.destroy', $election))
        ->assertSessionHas('status', 'Election deleted.');

    $this->assertDatabaseMissing('elections', ['id' => $election->id]);
});
