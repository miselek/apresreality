<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\Property;
use App\Models\PropertyEvent;
use App\Models\Task;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'activeContacts' => Contact::where('status', 'aktivni')->count(),
                'hotOpportunities' => Contact::where('tag', 'horka')->where('status', 'aktivni')->count(),
                'todayTasks' => Task::today()->count(),
                'totalContacts' => Contact::count(),
            ],
            'todayTasks' => Task::with('contact')
                ->today()
                ->orderByRaw("CASE priority WHEN 'vysoka' THEN 1 WHEN 'stredni' THEN 2 WHEN 'nizka' THEN 3 END")
                ->limit(10)
                ->get(),
            'hotContacts' => Contact::with('process.steps')
                ->where('tag', 'horka')
                ->where('status', 'aktivni')
                ->limit(5)
                ->get(),
            'recentActivity' => ActivityLog::with('contact')
                ->latest()
                ->limit(15)
                ->get(),
            'propertyStats' => [
                'activeListings' => Property::active()->count(),
                'inAdvertising' => Property::where('status', PropertyStatus::Inzerce)->count(),
                'reserved' => Property::where('status', PropertyStatus::Rezervace)->count(),
            ],
            'recentProperties' => Property::with(['photos' => function ($q) {
                $q->where('is_primary', true)->orWhere('order', 0)->limit(1);
            }, 'contact:id,name'])->active()->latest()->limit(3)->get()->each(function ($p) {
                $p->append(['progress', 'primary_photo']);
            }),
            'todayEvents' => PropertyEvent::with(['property:id,name', 'contact:id,name'])
                ->whereDate('starts_at', today())
                ->where('is_completed', false)
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
