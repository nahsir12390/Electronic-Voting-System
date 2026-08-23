<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function store(Request $request, Election $election): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'manifesto' => ['nullable', 'string', 'max:1000'],
        ]);

        $election->candidates()->create($validated);

        return back()->with('status', 'Candidate added.');
    }

    public function update(Request $request, Candidate $candidate): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'manifesto' => ['nullable', 'string', 'max:1000'],
        ]);

        $candidate->update($validated);

        return back()->with('status', 'Candidate updated.');
    }

    public function destroy(Request $request, Candidate $candidate): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        if ($candidate->votes()->exists()) {
            return back()->withErrors('Candidates with recorded votes cannot be deleted.');
        }

        $candidate->delete();

        return back()->with('status', 'Candidate deleted.');
    }
}
