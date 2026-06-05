<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed();
    Storage::fake('public');
});

test('admin can create event with custom banner files and display content', function () {
    $admin = User::factory()->create([
        'role' => 'owner'
    ]);

    $bgFile = UploadedFile::fake()->image('custom_bg.png');
    $mainFile = UploadedFile::fake()->image('custom_main.png');

    $response = $this->actingAs($admin)
        ->post(route('admin.events.store'), [
            'name' => 'NEW LUXURY CAMPAIGN',
            'short_name' => 'Luxury',
            'start_date' => now()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDays(10)->format('Y-m-d\TH:i'),
            'theme_color' => '#000000',
            'text_color' => '#ffffff',
            'background_image' => $bgFile,
            'main_image' => $mainFile,
            'display_title' => 'THE NEW COUTURE AGE',
            'display_description' => 'A wonderful selection of tailored suits.',
        ]);

    $response->assertRedirect(route('admin.events.index'));
    
    // Assert event persisted in database
    $event = Event::where('name', 'NEW LUXURY CAMPAIGN')->first();
    expect($event)->not->toBeNull();
    expect($event->display_title)->toBe('THE NEW COUTURE AGE');
    expect($event->display_description)->toBe('A wonderful selection of tailored suits.');
    expect($event->background_image)->not->toBeNull();
    expect($event->main_image)->not->toBeNull();

    // Assert files stored in fake public storage
    Storage::disk('public')->assertExists($event->background_image);
    Storage::disk('public')->assertExists($event->main_image);
});

test('admin can update event with custom banner files and display content', function () {
    $admin = User::factory()->create([
        'role' => 'manager'
    ]);

    $event = Event::create([
        'name' => 'ORIGINAL CAMPAIGN',
        'short_name' => 'Original',
        'start_date' => now()->subDays(1),
        'end_date' => now()->addDays(5),
        'theme_color' => '#111111',
        'text_color' => '#eeeeee',
    ]);

    $bgFile = UploadedFile::fake()->image('new_bg.jpg');
    $mainFile = UploadedFile::fake()->image('new_main.jpg');

    $response = $this->actingAs($admin)
        ->put(route('admin.events.update', $event->id), [
            'name' => 'UPDATED CAMPAIGN',
            'short_name' => 'Updated',
            'start_date' => now()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDays(12)->format('Y-m-d\TH:i'),
            'theme_color' => '#222222',
            'text_color' => '#dddddd',
            'background_image' => $bgFile,
            'main_image' => $mainFile,
            'display_title' => 'UPDATED DISPLAY TITLE',
            'display_description' => 'New description details.',
        ]);

    $response->assertRedirect(route('admin.events.index'));

    $event->refresh();
    expect($event->name)->toBe('UPDATED CAMPAIGN');
    expect($event->display_title)->toBe('UPDATED DISPLAY TITLE');
    expect($event->display_description)->toBe('New description details.');
    
    Storage::disk('public')->assertExists($event->background_image);
    Storage::disk('public')->assertExists($event->main_image);
});

test('frontend views render customized banner details if active', function () {
    $event = Event::create([
        'name' => 'ACTIVE BANNER CAMPAIGN',
        'short_name' => 'Active',
        'start_date' => now()->subMinutes(10),
        'end_date' => now()->addDays(2),
        'theme_color' => '#ff0000',
        'text_color' => '#ffff00',
        'background_image' => 'events/backgrounds/fake_bg.png',
        'main_image' => 'events/mains/fake_main.png',
        'display_title' => 'SUPER SALE EVENT',
        'display_description' => 'This is a description content.',
    ]);

    // Request Home page
    $homeResponse = $this->get('/');
    $homeResponse->assertStatus(200);
    $homeResponse->assertSee('events/backgrounds/fake_bg.png');
    $homeResponse->assertSee('events/mains/fake_main.png');
    $homeResponse->assertSee('SUPER SALE EVENT');
    $homeResponse->assertSee('This is a description content.');

    // Request Collections page
    $collectionsResponse = $this->get('/collections');
    $collectionsResponse->assertStatus(200);
    $collectionsResponse->assertSee('events/backgrounds/fake_bg.png');
    $collectionsResponse->assertSee('events/mains/fake_main.png');
    $collectionsResponse->assertSee('SUPER SALE EVENT');
    $collectionsResponse->assertSee('This is a description content.');
});
