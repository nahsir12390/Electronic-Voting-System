<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $voters = User::query()
            ->where('is_admin', false)
            ->withCount('votes')
            ->latest()
            ->get();

        return view('admin.voters.index', [
            'voters' => $voters,
            'votedCount' => $voters->where('votes_count', '>', 0)->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        return back()->with('status', 'Voter account created.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        abort_if($user->is_admin, 404);

        if ($user->votes()->exists()) {
            return back()->withErrors('Voters with recorded votes cannot be deleted.');
        }

        $user->delete();

        return back()->with('status', 'Voter account deleted.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}