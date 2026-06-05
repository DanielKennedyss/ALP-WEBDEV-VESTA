<?php

use App\Models\User;
use App\Models\Address;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can store a new address via profile endpoint', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post('/profile/addresses', [
            'label' => 'Office',
            'province_name' => 'DKI Jakarta',
            'city_name' => 'Jakarta Selatan',
            'full_address' => 'Sudirman St. No. 12',
            'is_default' => 1,
        ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('open-addresses-tab', true);
    
    $this->assertDatabaseHas('addresses', [
        'user_id' => $user->id,
        'label' => 'Office',
        'province_name' => 'DKI Jakarta',
        'city_name' => 'Jakarta Selatan',
        'full_address' => 'Sudirman St. No. 12',
        'is_default' => 1,
    ]);
});

test('user can update an address', function () {
    $user = User::factory()->create();
    $address = Address::create([
        'user_id' => $user->id,
        'label' => 'Home',
        'province_name' => 'Jawa Timur',
        'city_name' => 'Surabaya',
        'full_address' => 'Mulyorejo St. 10',
        'is_default' => 1,
    ]);

    $response = $this
        ->actingAs($user)
        ->put("/profile/addresses/{$address->id}", [
            'label' => 'Home Updated',
            'province_name' => 'Jawa Timur',
            'city_name' => 'Surabaya',
            'full_address' => 'Mulyorejo St. 12',
            'is_default' => 0,
        ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHas('open-addresses-tab', true);
    
    $this->assertDatabaseHas('addresses', [
        'id' => $address->id,
        'label' => 'Home Updated',
        'full_address' => 'Mulyorejo St. 12',
    ]);
});

test('user can delete an address', function () {
    $user = User::factory()->create();
    $address = Address::create([
        'user_id' => $user->id,
        'label' => 'Home',
        'province_name' => 'Jawa Timur',
        'city_name' => 'Surabaya',
        'full_address' => 'Mulyorejo St. 10',
        'is_default' => 1,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete("/profile/addresses/{$address->id}");

    $response->assertRedirect('/profile');
    $response->assertSessionHas('open-addresses-tab', true);
    $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
});

test('checkout saves the address if checked and avoids duplication', function () {
    $category = Category::create(['name' => 'Test Cat']);
    $product = Product::create([
        'name' => 'Premium Dress',
        'sku' => 'PD-001',
        'description' => 'A fine dress',
        'price' => 150000,
        'category_id' => $category->id,
        'gender' => 'Female',
        'image_path' => 'https://example.com/dress.jpg',
    ]);
    
    $user = User::factory()->create();
    
    $cartData = [
        'pd_001_s' => [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'size' => 'S',
            'image_path' => $product->image_path,
        ]
    ];
    
    $response = $this
        ->actingAs($user)
        ->withSession(['cart' => $cartData])
        ->post('/checkout/process', [
            'checked_items' => 'pd_001_s',
            'save_address' => '1',
            'save_address_label' => 'Home Address',
            'province_name' => 'Jawa Timur',
            'city_name' => 'Surabaya',
            'raw_address' => 'Mulyorejo St. 10',
            'shipping_address' => 'Mulyorejo St. 10, Surabaya, Jawa Timur',
            'shipping_courier' => 'jne',
            'shipping_service' => 'reg',
            'shipping_cost' => 15000,
        ]);
        
    $response->assertOk();
    
    $this->assertDatabaseHas('addresses', [
        'user_id' => $user->id,
        'label' => 'Home Address',
        'province_name' => 'Jawa Timur',
        'city_name' => 'Surabaya',
        'full_address' => 'Mulyorejo St. 10',
    ]);
    
    expect($user->addresses()->count())->toBe(1);
    
    $response2 = $this
        ->actingAs($user)
        ->withSession(['cart' => $cartData])
        ->post('/checkout/process', [
            'checked_items' => 'pd_001_s',
            'save_address' => '1',
            'save_address_label' => 'Home Address',
            'province_name' => 'Jawa Timur',
            'city_name' => 'Surabaya',
            'raw_address' => 'Mulyorejo St. 10',
            'shipping_address' => 'Mulyorejo St. 10, Surabaya, Jawa Timur',
            'shipping_courier' => 'jne',
            'shipping_service' => 'reg',
            'shipping_cost' => 15000,
        ]);
        
    $response2->assertOk();
    
    expect($user->addresses()->count())->toBe(1);
});
