<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;


class ProductApiTest extends TestCase
{
    use DatabaseMigrations;

    public function test_can_get_all_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_get_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $product->id]);
    }

    public function test_get_non_existing_product_returns_404()
    {
        $response = $this->getJson('/api/products/999999');

        $response->assertStatus(404);
    }

    public function test_create_product_requires_fields()
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'code', 'price', 'quantity']);
    }

    public function test_can_create_product()
    {
        $data = [
            'name' => 'Test product',
            'code' => 'PRD-001',
            'description' => 'Test description',
            'price' => 1000,
            'quantity' => 10,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test product']);

        $this->assertDatabaseHas('products', ['code' => 'PRD-001']);
    }
}
