<?php

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $election = Election::query()
            ->with(['candidates' => fn ($query) => $query->withCount('votes')])
            ->withCount('votes')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('home', compact('election'));
    }
}