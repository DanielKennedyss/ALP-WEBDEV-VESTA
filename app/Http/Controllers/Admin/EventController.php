<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * List all events with product count.
     */
    public function index(): View
    {
        $events = Event::withCount('products')
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show a read-only detail view for an ended event.
     */
    public function show($id): View
    {
        $event = Event::with(['products.category'])->findOrFail($id);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the Create Event form.
     */
    public function create(): View
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        return view('admin.events.create', compact('products'));
    }

    /**
     * Persist a new event and sync product assignments.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'short_name'   => 'required|string|max:100',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'theme_color'  => 'required|string|max:7',
            'text_color'   => 'required|string|max:7',
            'banner_image' => 'nullable|url|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'main_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'display_title'    => 'nullable|string|max:255',
            'display_description' => 'nullable|string',
        ], [
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
        ]);

        DB::beginTransaction();
        try {
            $eventData = collect($validated)->except(['background_image', 'main_image'])->toArray();
            $event = Event::create($eventData);

            if ($request->hasFile('background_image')) {
                $path = $request->file('background_image')->store('events/backgrounds', 'public');
                $event->background_image = $path;
            }
            if ($request->hasFile('main_image')) {
                $path = $request->file('main_image')->store('events/mains', 'public');
                $event->main_image = $path;
            }
            $event->save();

            // Sync product assignments (sync with empty array if none selected)
            $productIds = $request->input('product_ids', []);
            $event->products()->sync($productIds);

            DB::commit();
            return redirect()
                ->route('admin.events.index')
                ->with('success', 'Collection "' . $event->name . '" created and products assigned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create Event Error: ' . $e->getMessage());
            return back()
                ->with('error', 'Something went wrong while creating the collection. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the Edit Event form pre-populated with existing data.
     */
    public function edit($id): View|RedirectResponse
    {
        $event = Event::with('products')->findOrFail($id);

        // Backend guard: ended events are immutable
        if ($event->end_date->isPast()) {
            return redirect()
                ->route('admin.events.index')
                ->with('error', 'Ended events cannot be modified. You can view "' . $event->name . '" in read-only mode.');
        }

        $assignedProductIds = $event->products->pluck('id')->toArray();

        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        return view('admin.events.edit', compact('event', 'products', 'assignedProductIds'));
    }

    /**
     * Update an existing event and re-sync product assignments.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        // Backend guard: ended events are immutable
        if ($event->end_date->isPast()) {
            return redirect()
                ->route('admin.events.index')
                ->with('error', 'Ended events cannot be modified.');
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'short_name'   => 'required|string|max:100',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'theme_color'  => 'required|string|max:7',
            'text_color'   => 'required|string|max:7',
            'banner_image' => 'nullable|url|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'main_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'display_title'    => 'nullable|string|max:255',
            'display_description' => 'nullable|string',
        ], [
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
        ]);

        DB::beginTransaction();
        try {
            $eventData = collect($validated)->except(['background_image', 'main_image'])->toArray();
            $event->update($eventData);

            if ($request->hasFile('background_image')) {
                $path = $request->file('background_image')->store('events/backgrounds', 'public');
                $event->background_image = $path;
            }
            if ($request->hasFile('main_image')) {
                $path = $request->file('main_image')->store('events/mains', 'public');
                $event->main_image = $path;
            }
            $event->save();

            // Re-sync product assignments
            $productIds = $request->input('product_ids', []);
            $event->products()->sync($productIds);

            DB::commit();
            return redirect()
                ->route('admin.events.index')
                ->with('success', 'Collection "' . $event->name . '" updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Event Error: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to update collection. Please try again.')
                ->withInput();
        }
    }

    /**
     * Permanently delete an event (pivot records cascade automatically).
     */
    public function destroy($id): RedirectResponse
    {
        $event = Event::findOrFail($id);

        // Backend guard: ended events are immutable
        if ($event->end_date->isPast()) {
            return redirect()
                ->route('admin.events.index')
                ->with('error', 'Ended events cannot be deleted.');
        }

        DB::beginTransaction();
        try {
            // Detach all products before deleting to handle databases without cascade
            $event->products()->detach();
            $event->delete();

            DB::commit();
            return redirect()
                ->route('admin.events.index')
                ->with('success', 'Collection deleted permanently.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Event Error: ' . $e->getMessage());
            return redirect()
                ->route('admin.events.index')
                ->with('error', 'Failed to delete the collection.');
        }
    }
}
