<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.elections.index', [
            'elections' => Election::with(['candidates' => fn ($query) => $query->withCount('votes')])
                ->withCount(['candidates', 'votes'])
                ->latest()
                ->get(),
        ]);
    }

    public function show(Request $request, Election $election): View
    {
        $this->authorizeAdmin($request);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'candidate' => ['nullable', 'integer'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $candidateId = isset($filters['candidate']) ? (int) $filters['candidate'] : null;

        $election->load([
            'candidates' => fn ($query) => $query->withCount('votes'),
        ])->loadCount('votes');

        $voterLedger = $election->votes()
            ->with(['user', 'candidate'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($candidateId, function ($query) use ($candidateId, $election) {
                $candidateExists = $election->candidates->contains('id', $candidateId);

                if ($candidateExists) {
                    $query->where('candidate_id', $candidateId);
                }
            })
            ->latest()
            ->get();

        return view('admin.elections.show', [
            'election' => $election,
            'ledgerFilters' => [
                'search' => $search,
                'candidate' => $candidateId,
            ],
            'voterLedger' => $voterLedger,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'show_results' => ['nullable', 'boolean'],
        ]);

        Election::create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'show_results' => $request->boolean('show_results', true),
        ]);

        return back()->with('status', 'Election created.');
    }

    public function update(Request $request, Election $election): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'show_results' => ['nullable', 'boolean'],
        ]);

        $election->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'show_results' => $request->boolean('show_results'),
        ]);

        return back()->with('status', 'Election updated.');
    }

    public function destroy(Request $request, Election $election): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $election->delete();

        return back()->with('status', 'Election deleted.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
