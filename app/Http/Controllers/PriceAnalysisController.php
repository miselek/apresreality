<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\PriceAnalysis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class PriceAnalysisController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('PriceAnalyses/Index', [
            'analyses' => PriceAnalysis::with('contact')->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PriceAnalyses/Create', [
            'contacts' => Contact::orderBy('name')->get(['id', 'name']),
            'hasValuoKey' => !empty(config('services.valuo.api_key')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
            'address' => 'required|string|max:500',
            'area' => 'required|numeric|min:1',
            'property_type' => 'required|string',
            'condition' => 'required|string',
            'floor' => 'nullable|integer',
            'ownership' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
        ]);

        // Try Valuo API if key available and no manual price given
        if (empty($validated['estimated_price']) && config('services.valuo.api_key')) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.valuo.api_key'),
                ])->post('https://www.valuo.cz/api/v1/estimate', [
                    'address' => $validated['address'],
                    'area' => $validated['area'],
                    'type' => $validated['property_type'],
                    'condition' => $validated['condition'],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $validated['estimated_price'] = $data['estimated_price'] ?? null;
                    $validated['comparables'] = $data['comparables'] ?? null;
                }
            } catch (\Exception $e) {
                // API call failed, continue with manual entry
            }
        }

        $analysis = PriceAnalysis::create($validated);

        return redirect()->route('price-analyses.show', $analysis)
            ->with('success', 'Cenová analýza byla vytvořena.');
    }

    public function show(PriceAnalysis $priceAnalysis): Response
    {
        $priceAnalysis->load('contact');

        return Inertia::render('PriceAnalyses/Show', [
            'analysis' => $priceAnalysis,
        ]);
    }

    public function generateReport(PriceAnalysis $priceAnalysis): RedirectResponse
    {
        $priceAnalysis->load('contact');

        $pdf = Pdf::loadView('reports.price-analysis', [
            'analysis' => $priceAnalysis,
        ]);

        $filename = 'cenova-analyza-' . $priceAnalysis->id . '.pdf';
        $path = 'reports/' . $filename;

        \Storage::disk('public')->put($path, $pdf->output());

        $priceAnalysis->update(['report_url' => '/storage/' . $path]);

        return back()->with('success', 'PDF report byl vygenerován.');
    }

    public function downloadReport(PriceAnalysis $priceAnalysis)
    {
        if (!$priceAnalysis->report_url) {
            return back()->with('error', 'Report ještě nebyl vygenerován.');
        }

        $path = str_replace('/storage/', '', $priceAnalysis->report_url);

        return \Storage::disk('public')->download($path, 'cenova-analyza-' . $priceAnalysis->id . '.pdf');
    }
}
