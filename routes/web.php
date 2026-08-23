<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('elections/{election}/vote', [VoteController::class, 'store'])->name('votes.store');

    Route::get('admin/elections', [ElectionController::class, 'index'])->name('admin.elections.index');
    Route::post('admin/elections', [ElectionController::class, 'store'])->name('admin.elections.store');
    Route::get('admin/elections/{election}', [ElectionController::class, 'show'])->name('admin.elections.show');
    Route::patch('admin/elections/{election}', [ElectionController::class, 'update'])->name('admin.elections.update');
    Route::delete('admin/elections/{election}', [ElectionController::class, 'destroy'])->name('admin.elections.destroy');
    Route::get('admin/voters', [VoterController::class, 'index'])->name('admin.voters.index');
    Route::post('admin/voters', [VoterController::class, 'store'])->name('admin.voters.store');
    Route::delete('admin/voters/{user}', [VoterController::class, 'destroy'])->name('admin.voters.destroy');
    Route::post('admin/elections/{election}/candidates', [CandidateController::class, 'store'])->name('admin.candidates.store');
    Route::patch('admin/candidates/{candidate}', [CandidateController::class, 'update'])->name('admin.candidates.update');
    Route::delete('admin/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('admin.candidates.destroy');
});

require __DIR__.'/settings.php';
