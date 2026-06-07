<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventSubcategory;
use App\Models\Product;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Clear tables with foreign key checks disabled depending on database driver
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table('event_subcategory_product')->truncate();
        DB::table('event_product')->truncate();
        DB::table('event_subcategories')->truncate();
        DB::table('events')->truncate();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Find products to associate
        $outerwearProducts = Product::whereIn('name', ['Midnight Velvet Blazer', 'Oversized Wool Coat'])->get();
        $tailoredProducts = Product::whereIn('name', ['Oxford Tailored Trousers', 'Minimalist Linen Shirt', 'Elegant Evening Dress'])->get();
        $accessoryProducts = Product::whereIn('name', ['Monogram Silk Tie', 'Classic Fedora Hat', 'Leather Belt'])->get();

        // 1. New Year's Festival
        $nyEvent = Event::create([
            'name' => 'NEW YEAR SOIREE',
            'start_date' => '2026-12-29 00:00:00',
            'end_date' => '2027-01-05 23:59:59',
            'theme_color' => '#0f172a', // Slate 900
            'text_color' => '#fbbf24',  // Gold
            'banner_image' => 'https://images.unsplash.com/photo-1546776310-eef45dd6d63c?w=1600&q=80',
            'short_name' => 'New Year',
            'background_image' => 'https://images.unsplash.com/photo-1513151233558-d860c5398176?w=1600&q=80',
            'main_image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=1600&q=80',
            'display_title' => 'NEW YEAR SOIREE',
            'display_description' => 'Ring in the luxury. Elegant evening wear and accessories for the perfect countdown.',
        ]);
        $nySub1 = EventSubcategory::create(['event_id' => $nyEvent->id, 'name' => 'New Year Sparkles']);
        $nySub2 = EventSubcategory::create(['event_id' => $nyEvent->id, 'name' => 'Midnight Elegance']);
        foreach ($accessoryProducts as $p) { $nySub1->products()->attach($p->id); $nyEvent->products()->attach($p->id); }
        foreach ($outerwearProducts as $p) { $nySub2->products()->attach($p->id); $nyEvent->products()->attach($p->id); }

        // 2. Valentine's Day Romance
        $valEvent = Event::create([
            'name' => "VALENTINE'S ROMANCE",
            'start_date' => '2026-02-07 00:00:00',
            'end_date' => '2026-02-15 23:59:59',
            'theme_color' => '#db2777', // Pink 600
            'text_color' => '#ffffff',  // White
            'banner_image' => 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?w=1600&q=80',
            'short_name' => "Valentine's",
            'background_image' => 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=1600&q=80',
            'main_image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&q=80',
            'display_title' => "VALENTINE'S ROMANCE",
            'display_description' => 'Celebrate love with curated collections, romantic silhouettes, and precious gifts.',
        ]);
        $valSub1 = EventSubcategory::create(['event_id' => $valEvent->id, 'name' => "Valentine's Gift Guide"]);
        $valSub2 = EventSubcategory::create(['event_id' => $valEvent->id, 'name' => 'Romantic Collection']);
        foreach ($accessoryProducts as $p) { $valSub1->products()->attach($p->id); $valEvent->products()->attach($p->id); }
        foreach ($tailoredProducts as $p) { $valSub2->products()->attach($p->id); $valEvent->products()->attach($p->id); }

        // 3. Mid-Year Summer Sale (ACTIVE TODAY, June 4, 2026)
        $summerEvent = Event::create([
            'name' => 'MID-YEAR SUMMER CARNIVAL',
            'start_date' => '2026-06-01 00:00:00',
            'end_date' => '2026-06-10 23:59:59',
            'theme_color' => '#0d9488', // Teal 600
            'text_color' => '#fef08a',  // Yellow 200
            'banner_image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&q=80',
            'short_name' => 'Summer',
            'background_image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1600&q=80',
            'main_image' => 'https://images.unsplash.com/photo-1502716119720-b23a93e5fe1b?w=1600&q=80',
            'display_title' => 'MID-YEAR SUMMER CARNIVAL',
            'display_description' => 'Sun-kissed luxury. Embrace the warmth with linen shirts, lightweight fabrics, and seasonal accents.',
        ]);
        $sumSub1 = EventSubcategory::create(['event_id' => $summerEvent->id, 'name' => 'Summer Vibe Wear']);
        $sumSub2 = EventSubcategory::create(['event_id' => $summerEvent->id, 'name' => 'Sunny Accessories']);
        foreach ($tailoredProducts as $p) { $sumSub1->products()->attach($p->id); $summerEvent->products()->attach($p->id); }
        foreach ($accessoryProducts as $p) { $sumSub2->products()->attach($p->id); $summerEvent->products()->attach($p->id); }

        // 4. Halloween Spooky Deals
        $halEvent = Event::create([
            'name' => 'HALLOWEEN SPOOKTACULAR',
            'start_date' => '2026-10-25 00:00:00',
            'end_date' => '2026-11-02 23:59:59',
            'theme_color' => '#ea580c', // Orange 600
            'text_color' => '#09090b',  // Zinc 950 (Dark text)
            'banner_image' => 'https://images.unsplash.com/photo-1508349937151-22b68b72d5b1?w=1600&q=80',
            'short_name' => 'Halloween',
            'background_image' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=1600&q=80',
            'main_image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1600&q=80',
            'display_title' => 'HALLOWEEN SPOOKTACULAR',
            'display_description' => 'Hauntingly elegant. Discover our dark theme collection and deep velvet statement pieces.',
        ]);
        $halSub1 = EventSubcategory::create(['event_id' => $halEvent->id, 'name' => 'Spooky Halloween Deals']);
        $halSub2 = EventSubcategory::create(['event_id' => $halEvent->id, 'name' => 'Midnight Velvet Specials']);
        foreach ($outerwearProducts as $p) { $halSub1->products()->attach($p->id); $halEvent->products()->attach($p->id); }
        foreach ($tailoredProducts as $p) { $halSub2->products()->attach($p->id); $halEvent->products()->attach($p->id); }

        // 5. Black Friday Frenzy
        $bfEvent = Event::create([
            'name' => 'BLACK FRIDAY SUPER SALE',
            'start_date' => '2026-11-20 00:00:00',
            'end_date' => '2026-11-30 23:59:59',
            'theme_color' => '#000000', // Black
            'text_color' => '#ef4444',  // Red 500
            'banner_image' => 'https://images.unsplash.com/photo-1540959733332-eab4deceeaf7?w=1600&q=80',
            'short_name' => 'Black Friday',
            'background_image' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=1600&q=80',
            'main_image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1600&q=80',
            'display_title' => 'BLACK FRIDAY SUPER SALE',
            'display_description' => 'The pinnacle of the season. Exceptional offers on our most coveted luxury items.',
        ]);
        $bfSub1 = EventSubcategory::create(['event_id' => $bfEvent->id, 'name' => 'Black Friday Doorbusters']);
        $bfSub2 = EventSubcategory::create(['event_id' => $bfEvent->id, 'name' => 'Midnight Markdown']);
        foreach ($outerwearProducts as $p) { $bfSub1->products()->attach($p->id); $bfEvent->products()->attach($p->id); }
        foreach ($accessoryProducts as $p) { $bfSub2->products()->attach($p->id); $bfEvent->products()->attach($p->id); }

        // 6. Christmas Splendor
        $xmasEvent = Event::create([
            'name' => 'CHRISTMAS SPLENDOR',
            'start_date' => '2026-12-15 00:00:00',
            'end_date' => '2026-12-28 23:59:59',
            'theme_color' => '#15803d', // Green 700
            'text_color' => '#fca5a5',  // Red 300
            'banner_image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1600&q=80',
            'short_name' => 'Christmas',
            'background_image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1600&q=80',
            'main_image' => 'https://images.unsplash.com/photo-1608096299210-db7e38487075?w=1600&q=80',
            'display_title' => 'CHRISTMAS SPLENDOR',
            'display_description' => 'Unwrap elegance. Premium winter outerwear, cashmere layers, and luxury holiday gifts.',
        ]);
        $xmasSub1 = EventSubcategory::create(['event_id' => $xmasEvent->id, 'name' => 'Christmas Gifts & Warmth']);
        $xmasSub2 = EventSubcategory::create(['event_id' => $xmasEvent->id, 'name' => 'Festive Outerwear']);
        foreach ($tailoredProducts as $p) { $xmasSub1->products()->attach($p->id); $xmasEvent->products()->attach($p->id); }
        foreach ($outerwearProducts as $p) { $xmasSub2->products()->attach($p->id); $xmasEvent->products()->attach($p->id); }
    }
}
