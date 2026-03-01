<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Contract;
use App\Models\ContractTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class ContractController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contracts/Index', [
            'contracts' => Contract::with(['template', 'contact'])->latest()->paginate(20),
            'templates' => ContractTemplate::all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Contracts/Create', [
            'templates' => ContractTemplate::all(),
            'contacts' => Contact::orderBy('name')->get(['id', 'name', 'email', 'phone']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:contract_templates,id',
            'contact_id' => 'required|exists:contacts,id',
            'data' => 'nullable|array',
        ]);

        $contract = Contract::create([
            'template_id' => $validated['template_id'],
            'contact_id' => $validated['contact_id'],
            'data' => $validated['data'] ?? [],
            'status' => 'koncept',
        ]);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Smlouva byla vytvořena.');
    }

    public function show(Contract $contract): Response
    {
        $contract->load(['template', 'contact']);

        return Inertia::render('Contracts/Show', [
            'contract' => $contract,
        ]);
    }

    public function validateContract(Contract $contract): RedirectResponse
    {
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            return back()->with('error', 'Anthropic API klíč není nastaven.');
        }

        $contract->load(['template', 'contact']);
        $contractText = json_encode($contract->data, JSON_UNESCAPED_UNICODE);
        $templateName = $contract->template?->name ?? 'neznámá';

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 4096,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Zkontroluj, zda navrhovaná úprava smlouvy je v souladu s původní šablonou. Upozorni na jakékoliv právně rizikové nebo nestandardní změny.\n\nTyp smlouvy: {$templateName}\nData smlouvy: {$contractText}",
                    ],
                ],
                'system' => 'Jsi právní expert na české nemovitostní právo. Analyzuj smlouvu a identifikuj potenciální problémy, chybějící klauzule, a právní rizika. Odpověz v češtině.',
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $validationText = $result['content'][0]['text'] ?? 'Validace nevrátila výsledek.';

                $contract->update([
                    'status' => 'zvalidovano',
                    'validation_result' => $validationText,
                ]);

                return back()->with('success', 'Smlouva byla zvalidována AI.');
            }

            return back()->with('error', 'AI validace selhala: ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Chyba při AI validaci: ' . $e->getMessage());
        }
    }

    public function verifyClient(Contract $contract): RedirectResponse
    {
        $contract->load('contact');
        $contact = $contract->contact;

        $results = ['ares' => null, 'isir' => null];

        // ARES check
        try {
            $response = Http::get("https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/vyhledat", [
                'obchodniJmeno' => $contact->name,
            ]);

            if ($response->successful()) {
                $results['ares'] = $response->json();
            }
        } catch (\Exception $e) {
            $results['ares'] = ['error' => $e->getMessage()];
        }

        // ISIR check
        try {
            $response = Http::get('https://isir.justice.cz/isir/common/stat.do', [
                'nazev' => $contact->name,
            ]);

            $results['isir'] = [
                'checked' => true,
                'name' => $contact->name,
                'status' => $response->successful() ? 'checked' : 'error',
            ];
        } catch (\Exception $e) {
            $results['isir'] = ['error' => $e->getMessage()];
        }

        $contract->update([
            'verification_result' => $results,
        ]);

        return back()->with('success', 'Ověření klienta bylo dokončeno.');
    }

    public function download(Contract $contract)
    {
        $contract->load(['template', 'contact']);

        $pdf = Pdf::loadView('reports.contract', [
            'contract' => $contract,
        ]);

        return $pdf->download('smlouva-' . $contract->id . '.pdf');
    }
}
