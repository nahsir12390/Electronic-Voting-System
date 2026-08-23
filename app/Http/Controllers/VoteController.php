<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    public function store(Request $request, Election $election): RedirectResponse
    {
        abort_unless($election->isOpen(), 403, 'This election is not open for voting.');

        $validated = $request->validate([
            'candidate_id' => ['required', 'integer', 'exists:candidates,id'],
        ]);

        $candidate = $election->candidates()
            ->whereKey($validated['candidate_id'])
            ->first();

        abort_unless($candidate instanceof Candidate, 404);

        $existingVote = Vote::query()
            ->where('election_id', $election->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingVote) {
            return back()->with('status', 'You have already voted in this election.');
        }

        $vote = DB::transaction(function () use ($candidate, $election, $request): Vote {
            $payload = implode('|', [
                $election->id,
                $candidate->id,
                $request->user()->id,
                now()->timestamp,
                Str::random(40),
            ]);

            return Vote::create([
                'election_id' => $election->id,
                'candidate_id' => $candidate->id,
                'user_id' => $request->user()->id,
                'vote_hash' => hash('sha256', $payload),
                'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
                'user_agent_hash' => $request->userAgent() ? hash('sha256', $request->userAgent()) : null,
            ]);
        });

        return back()->with('status', 'Vote submitted securely. Receipt: '.$vote->vote_hash);
    }
}
