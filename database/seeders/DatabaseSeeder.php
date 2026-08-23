<?php

namespace Database\Seeders;

use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Election Admin',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        User::updateOrCreate(['email' => 'voter@example.com'], [
            'name' => 'Demo Voter',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        $election = Election::firstOrCreate(['title' => 'Student Council President'], [
            'title' => 'Student Council President',
            'description' => 'A sample election for demonstrating secure small-scale online voting.',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(7),
            'is_active' => true,
            'show_results' => true,
        ]);

        collect([
            [
                'name' => 'Amina Yusuf',
                'position' => 'President',
                'manifesto' => 'Improve student communication and organize monthly feedback meetings.',
            ],
            [
                'name' => 'Daniel Okafor',
                'position' => 'President',
                'manifesto' => 'Build a transparent project fund and support academic clubs.',
            ],
        ])->each(fn (array $candidate) => $election->candidates()->firstOrCreate([
            'name' => $candidate['name'],
        ], $candidate));
    }
}
