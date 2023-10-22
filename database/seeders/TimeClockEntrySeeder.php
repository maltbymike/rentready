<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TimeClockEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::role('Timeclock User')->get();

        $users->each(function (User $user) {
            for ($i = 0; $i < 5; $i++) {
                $user->timeClockEntries()->create([
                    'clock_out_at' => now()->subDays($i)->format('Y-m-d H:i:s'),
                    'clock_in_at' => now()->subDays($i)->subHours(9)->format('Y-m-d H:i:s'),
                ]);        
            }
        });
    }
}
