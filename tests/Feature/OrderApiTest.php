<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;


class OrderApiTest extends TestCase
{
    use DatabaseMigrations;

    public function test_create_order_fails_if_product_not_exists()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'products' => [
                ['id' => 99999, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['products.0.id']);
    }

    public function test_create_order_fails_if_not_enough_stock()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 1]);

        $data = [
            'user_id' => $user->id,
            'products' => [
                ['id' => $product->id, 'quantity' => 10]
            ]
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(422)
                 ->assertJsonFragment([
                     'message' => "Not enough stock for product \"{$product->code}\""
                 ]);
    }

    public function test_can_create_order_and_decrements_stock()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        $data = [
            'user_id' => $user->id,
            'products' => [
                ['id' => $product->id, 'quantity' => 3]
            ]
        ];

        $response = $this->postJson('/api/orders', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['user_id' => $user->id]);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 7,
        ]);
    }
}
