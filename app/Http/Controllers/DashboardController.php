<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $elections = Election::query()
            ->with(['candidates' => fn ($query) => $query->withCount('votes')])
            ->withCount('votes')
            ->latest()
            ->get();

        $votesByElection = Vote::query()
            ->where('user_id', $user->id)
            ->with('candidate')
            ->get()
            ->keyBy('election_id');

        return view('dashboard', [
            'elections' => $elections,
            'votesByElection' => $votesByElection,
        ]);
    }
}
