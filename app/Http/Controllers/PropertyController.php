<?php

namespace App\Http\Controllers;

use App\Enums\InterestType;
use App\Enums\PropertyPriceType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Contact;
use App\Models\PriceAnalysis;
use App\Models\Property;
use App\Models\PropertyEvent;
use App\Models\PropertyInterest;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['contact:id,name', 'photos' => function ($q) {
            $q->where('is_primary', true)->orWhere('order', 0)->limit(1);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        $properties = $query->latest()->paginate(12)->withQueryString();

        // Add computed attributes
        $properties->getCollection()->transform(function ($property) {
            $property->append(['progress', 'primary_photo', 'commission_computed']);
            return $property;
        });

        // Status counts for filter badges
        $statusCounts = Property::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('Nemovitosti/Index', [
            'properties' => $properties,
            'filters' => $request->only(['search', 'status', 'property_type', 'price_type']),
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create()
    {
        return Inertia::render('Nemovitosti/Create', [
            'contacts' => Contact::select('id', 'name', 'phone')->orderBy('name')->get(),
            'priceAnalyses' => PriceAnalysis::select('id', 'address', 'estimated_price')->orderBy('address')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'property_type' => 'required|string|in:' . implode(',', array_column(PropertyType::cases(), 'value')),
            'disposition' => 'nullable|string|max:50',
            'area' => 'nullable|numeric|min:0',
            'land_area' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|string|in:' . implode(',', array_column(PropertyPriceType::cases(), 'value')),
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'ad_budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'contact_id' => 'nullable|exists:contacts,id',
            'price_analysis_id' => 'nullable|exists:price_analyses,id',
            'notes' => 'nullable|string',
        ]);

        $property = Property::create($validated);

        return redirect()->route('nemovitosti.show', $property)
            ->with('success', 'Nemovitost byla vytvořena.');
    }

    public function show(Property $nemovitost)
    {
        $nemovitost->load([
            'contact:id,name,phone,email',
            'priceAnalysis',
            'photos',
            'interests.contact:id,name,phone,email',
            'events.contact:id,name',
        ]);

        $nemovitost->append(['progress', 'days_on_market', 'primary_photo', 'commission_computed', 'ad_remaining']);

        // Stats
        $stats = [
            'interest_count' => $nemovitost->interests->where('type', InterestType::Zajemce)->count()
                + $nemovitost->interests->where('type', InterestType::Navsteva)->count()
                + $nemovitost->interests->where('type', InterestType::Rezervace)->count(),
            'visit_count' => $nemovitost->interests->where('type', InterestType::Navsteva)->count(),
            'reservation_count' => $nemovitost->interests->where('type', InterestType::Rezervace)->count(),
            'event_count' => $nemovitost->events->count(),
            'upcoming_events' => $nemovitost->events->where('starts_at', '>=', now())->where('is_completed', false)->count(),
        ];

        $contacts = Contact::select('id', 'name', 'phone')->orderBy('name')->get();

        return Inertia::render('Nemovitosti/Show', [
            'property' => $nemovitost,
            'stats' => $stats,
            'contacts' => $contacts,
        ]);
    }

    public function edit(Property $nemovitost)
    {
        return Inertia::render('Nemovitosti/Edit', [
            'property' => $nemovitost,
            'contacts' => Contact::select('id', 'name', 'phone')->orderBy('name')->get(),
            'priceAnalyses' => PriceAnalysis::select('id', 'address', 'estimated_price')->orderBy('address')->get(),
        ]);
    }

    public function update(Request $request, Property $nemovitost)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'property_type' => 'required|string|in:' . implode(',', array_column(PropertyType::cases(), 'value')),
            'disposition' => 'nullable|string|max:50',
            'area' => 'nullable|numeric|min:0',
            'land_area' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_type' => 'required|string|in:' . implode(',', array_column(PropertyPriceType::cases(), 'value')),
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'ad_budget' => 'nullable|numeric|min:0',
            'ad_spent' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'contact_id' => 'nullable|exists:contacts,id',
            'price_analysis_id' => 'nullable|exists:price_analyses,id',
            'notes' => 'nullable|string',
        ]);

        $nemovitost->update($validated);

        return redirect()->route('nemovitosti.show', $nemovitost)
            ->with('success', 'Nemovitost byla aktualizována.');
    }

    public function destroy(Property $nemovitost)
    {
        // Delete photo files
        foreach ($nemovitost->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $nemovitost->delete();

        return redirect()->route('nemovitosti.index')
            ->with('success', 'Nemovitost byla smazána.');
    }

    public function advanceStatus(Property $nemovitost)
    {
        $nextStatus = PropertyStatus::nextStatus($nemovitost->status);

        if (!$nextStatus) {
            return back()->with('error', 'Nelze posunout stav dále.');
        }

        $nemovitost->status = $nextStatus;

        // Set published_at when entering inzerce
        if ($nextStatus === PropertyStatus::Inzerce && !$nemovitost->published_at) {
            $nemovitost->published_at = now();
        }

        // Set sold_at when entering prodano
        if ($nextStatus === PropertyStatus::Prodano) {
            $nemovitost->sold_at = now();
        }

        $nemovitost->save();

        return back()->with('success', "Stav byl posunut na: {$nextStatus->label()}");
    }

    public function uploadPhotos(Request $request, Property $nemovitost)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        $maxOrder = $nemovitost->photos()->max('order') ?? -1;
        $isFirst = $nemovitost->photos()->count() === 0;

        foreach ($request->file('photos') as $i => $file) {
            $path = $file->store("properties/{$nemovitost->id}", 'public');

            $nemovitost->photos()->create([
                'file_path' => $path,
                'order' => $maxOrder + $i + 1,
                'is_primary' => $isFirst && $i === 0,
            ]);
        }

        return back()->with('success', 'Fotky byly nahrány.');
    }

    public function deletePhoto(Property $nemovitost, PropertyPhoto $photo)
    {
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        return back()->with('success', 'Fotka byla smazána.');
    }

    public function reorderPhotos(Request $request, Property $nemovitost)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:property_photos,id',
            'order.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->order as $item) {
            PropertyPhoto::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return back()->with('success', 'Pořadí fotek bylo aktualizováno.');
    }

    public function setPrimaryPhoto(Property $nemovitost, PropertyPhoto $photo)
    {
        // Unset all primary
        $nemovitost->photos()->update(['is_primary' => false]);
        // Set this one
        $photo->update(['is_primary' => true]);

        return back()->with('success', 'Hlavní fotka byla nastavena.');
    }

    public function addInterest(Request $request, Property $nemovitost)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'type' => 'required|string|in:' . implode(',', array_column(InterestType::cases(), 'value')),
            'note' => 'nullable|string',
            'visited_at' => 'nullable|date',
        ]);

        $nemovitost->interests()->create($validated);

        return back()->with('success', 'Zájemce byl přidán.');
    }

    public function removeInterest(Property $nemovitost, PropertyInterest $interest)
    {
        $interest->delete();

        return back()->with('success', 'Zájemce byl odebrán.');
    }

    public function addEvent(Request $request, Property $nemovitost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:prohlidka,schuzka,foceni,jine',
            'contact_id' => 'nullable|exists:contacts,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $nemovitost->events()->create($validated);

        return back()->with('success', 'Událost byla vytvořena.');
    }

    public function updateEvent(Request $request, Property $nemovitost, PropertyEvent $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:prohlidka,schuzka,foceni,jine',
            'contact_id' => 'nullable|exists:contacts,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $event->update($validated);

        return back()->with('success', 'Událost byla aktualizována.');
    }

    public function deleteEvent(Property $nemovitost, PropertyEvent $event)
    {
        $event->delete();

        return back()->with('success', 'Událost byla smazána.');
    }

    public function completeEvent(Property $nemovitost, PropertyEvent $event)
    {
        $event->update(['is_completed' => true]);

        return back()->with('success', 'Událost byla dokončena.');
    }

    public function landing(Property $nemovitost)
    {
        $nemovitost->load(['photos', 'contact:id,name,phone,email']);
        $nemovitost->append(['primary_photo']);

        return Inertia::render('Nemovitosti/Landing', [
            'property' => $nemovitost,
        ]);
    }
}
