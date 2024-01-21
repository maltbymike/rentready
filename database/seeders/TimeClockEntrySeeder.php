<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
                if ($i % 2 == 0) {
                    $user->timeClockEntries()->createMany([
                        [
                            'clock_out_at' => now()->subDays($i)->format('Y-m-d H:i'),
                            'clock_in_at' => now()->subDays($i)->subHours(2)->format('Y-m-d H:i'),
                        ],
                        [
                            'clock_out_at' => now()->subDays($i)->subHours(3)->format('Y-m-d H:i'),
                            'clock_in_at' => now()->subDays($i)->subHours(5)->format('Y-m-d H:i'),
                        ],
                        [
                            'clock_out_at' => now()->subDays($i)->subHours(6)->format('Y-m-d H:i'),
                            'clock_in_at' => now()->subDays($i)->subHours(7)->format('Y-m-d H:i'),
                        ],
                        [
                            'clock_out_at' => now()->subDays($i)->subHours(7)->format('Y-m-d H:i'),
                            'clock_in_at' => now()->subDays($i)->subHours(9)->format('Y-m-d H:i'),
                        ],
                    ]);
                } elseif ($i % 3 == 0) {
                    $user->timeClockEntries()->createMany([
                        [
                            'clock_out_at' => now()->subDays($i)->format('Y-m-d H:i'),
                            'clock_in_at' => now()->subDays($i)->subHours(4)->format('Y-m-d H:i'),
                        ],
                        [
                            'clock_out_at' => now()->subDays($i)->subHours(5)->format('Y-m-d H:i'),
                            'clock_in_at' => now()->subDays($i)->subHours(9)->format('Y-m-d H:i'),
                        ],
                    ]);
                } else {
                    $user->timeClockEntries()->create([
                        'clock_out_at' => now()->subDays($i)->format('Y-m-d H:i'),
                        'clock_in_at' => now()->subDays($i)->subHours(9)->format('Y-m-d H:i'),
                    ]);
                }
            }
        });
    }
}
