<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $products = Product::factory(20)->create();

        $orders = Order::factory(3)->create([
            'user_id' => $user->id,
        ]);

        foreach ($orders as $order) {
            $randomProducts = $products->random(rand(1, 5));

            $attachData = $randomProducts->mapWithKeys(function ($product) {
                return [
                    $product->id => [
                        'quantity' => rand(1, 5),
                        'price' => $product->price,
                    ],
                ];
            })->toArray();

            $order->products()->attach($attachData);
        }
    }
}
