<?php

use App\Models\Event;
use App\Models\Product;
use App\Models\Category;

beforeEach(function () {
    // Seed database for lookup
    $this->seed();
});

test('catalog page displays all products when no event filter is present', function () {
    $response = $this->get('/collections');

    $response->assertStatus(200);
    $response->assertViewHas('products');
    $response->assertViewHas('filteredEvent', null);
});

test('catalog page filters products when filter_event is present', function () {
    // Create an event manually
    $event = Event::create([
        'name' => 'TEST SPECIAL SALE',
        'start_date' => now()->subDays(1),
        'end_date' => now()->addDays(5),
        'theme_color' => '#123456',
        'text_color' => '#ffffff',
        'banner_image' => 'https://example.com/test.jpg',
        'short_name' => 'Test Sale',
    ]);

    // Get or create category
    $category = Category::first() ?? Category::create(['name' => 'Test Category']);

    // Create a product associated with the event
    $product1 = Product::create([
        'name' => 'Event Product Unique Name',
        'sku' => 'EP-001',
        'description' => 'Test event product description',
        'price' => 100000,
        'category_id' => $category->id,
        'gender' => 'Unisex',
        'image_path' => 'https://example.com/img.jpg',
    ]);
    $product1->events()->attach($event->id);

    // Create a product NOT associated with the event
    $product2 = Product::create([
        'name' => 'Regular Product Unique Name',
        'sku' => 'RP-001',
        'description' => 'Test regular product description',
        'price' => 150000,
        'category_id' => $category->id,
        'gender' => 'Unisex',
        'image_path' => 'https://example.com/img2.jpg',
    ]);

    // Request the filtered catalog
    $response = $this->get(route('collections.index', ['filter_event' => $event->id]));

    $response->assertStatus(200);
    $response->assertViewHas('filteredEvent');
    
    $filtered = $response->viewData('filteredEvent');
    expect($filtered->id)->toBe($event->id);

    $products = $response->viewData('products');
    
    // Get product IDs in the collection
    $productIds = collect($products->items())->pluck('id')->all();
    
    expect($productIds)->toContain($product1->id);
    expect($productIds)->not->toContain($product2->id);
});
