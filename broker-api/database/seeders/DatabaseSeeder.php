<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use App\OrderStatus;
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
        $john = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => bcrypt('secret'),
            'balance' => 50000.00,
        ]);

        $jane = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@doe.com',
            'password' => bcrypt('secret'),
            'balance' => 50000.00,
        ]);

        Asset::factory()->create([
            'user_id' => $john->id,
            'symbol' => 'BTC',
            'amount' => 0.5,
            'locked_amount' => 0,
        ]);

        Asset::factory()->create([
            'user_id' => $john->id,
            'symbol' => 'ETH',
            'amount' => 5.0,
            'locked_amount' => 0,
        ]);

        Asset::factory()->create([
            'user_id' => $jane->id,
            'symbol' => 'BTC',
            'amount' => 0.8,
            'locked_amount' => 0,
        ]);

        Asset::factory()->create([
            'user_id' => $jane->id,
            'symbol' => 'ETH',
            'amount' => 20.0,
            'locked_amount' => 0,
        ]);


    }
}
