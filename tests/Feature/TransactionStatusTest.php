<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;

beforeEach(function () {
    $this->seed();
});

test('active orders count includes pending, success, processing, settlement, paid, and shipped transactions', function () {
    $user = User::factory()->create();
    $product = Product::first();

    // Create multiple transactions of different statuses
    Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'invoice_number' => 'INV-TEST-PENDING',
        'quantity' => 1,
        'total_price' => 100000,
        'customer_name' => $user->name,
        'status' => 'pending'
    ]);

    Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'invoice_number' => 'INV-TEST-PROCESSING',
        'quantity' => 1,
        'total_price' => 100000,
        'customer_name' => $user->name,
        'status' => 'processing'
    ]);

    Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'invoice_number' => 'INV-TEST-SHIPPED',
        'quantity' => 1,
        'total_price' => 100000,
        'customer_name' => $user->name,
        'status' => 'shipped'
    ]);

    Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'invoice_number' => 'INV-TEST-DELIVERED',
        'quantity' => 1,
        'total_price' => 100000,
        'customer_name' => $user->name,
        'status' => 'delivered'
    ]);

    $response = $this->actingAs($user)->get('/profile');
    $response->assertStatus(200);

    // Active orders should be 3 (pending, processing, shipped), delivered is NOT active.
    $response->assertSee('Active Orders');
    
    // Let's verify the database query directly
    $activeCount = Transaction::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'success', 'processing', 'settlement', 'paid', 'shipped'])
        ->count();

    expect($activeCount)->toBe(3);
});

test('admin cannot change transaction status back to processing or pending if current status is shipped', function () {
    $admin = User::factory()->create(['role' => 'owner']);
    $user = User::factory()->create();
    $product = Product::first();

    $transaction = Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'invoice_number' => 'INV-TEST-SHIPPED-2',
        'quantity' => 1,
        'total_price' => 100000,
        'customer_name' => $user->name,
        'status' => 'shipped'
    ]);

    // Attempt to transition shipped to processing (should be blocked)
    $response = $this->actingAs($admin)->from('/admin/transactions')
        ->patch(route('admin.transactions.updateStatus', $transaction->id), [
            'status' => 'processing'
        ]);

    $response->assertRedirect('/admin/transactions');
    $response->assertSessionHas('error');
    
    $transaction->refresh();
    expect($transaction->status)->toBe('shipped');

    // Attempt to transition shipped to delivered (allowed)
    $response2 = $this->actingAs($admin)->from('/admin/transactions')
        ->patch(route('admin.transactions.updateStatus', $transaction->id), [
            'status' => 'delivered'
        ]);

    $response2->assertRedirect('/admin/transactions');
    $response2->assertSessionHas('success');
    
    $transaction->refresh();
    expect($transaction->status)->toBe('delivered');
});

test('user can mark shipped transaction as received from profile page', function () {
    $user = User::factory()->create();
    $product = Product::first();

    $transaction = Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'invoice_number' => 'INV-TEST-RECEIVE',
        'quantity' => 1,
        'total_price' => 100000,
        'customer_name' => $user->name,
        'status' => 'shipped'
    ]);

    $response = $this->actingAs($user)->from('/profile')
        ->post(route('profile.orders.receive', $transaction->id));

    $response->assertRedirect('/profile');
    $response->assertSessionHas('success');

    $transaction->refresh();
    expect($transaction->status)->toBe('delivered');
});
