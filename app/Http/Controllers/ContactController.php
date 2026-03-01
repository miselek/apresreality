<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Contact::with('process');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        if ($source = $request->input('source')) {
            $query->where('source', $source);
        }
        if ($tag = $request->input('tag')) {
            $query->where('tag', $tag);
        }

        $contacts = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'status', 'type', 'source', 'tag']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Contacts/Create', [
            'processes' => Process::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'source' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'tag' => 'nullable|string',
            'process_id' => 'nullable|exists:processes,id',
            'current_step' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $contact = Contact::create($validated);

        ActivityLog::create([
            'contact_id' => $contact->id,
            'type' => 'poznamka',
            'description' => 'Kontakt vytvořen v systému',
        ]);

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Kontakt byl úspěšně vytvořen.');
    }

    public function show(Contact $contact): Response
    {
        $contact->load(['process.steps', 'tasks' => function ($q) {
            $q->orderBy('is_done')->orderBy('due_date');
        }, 'activityLogs' => function ($q) {
            $q->latest()->limit(30);
        }]);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact,
            'processes' => Process::with('steps')->get(),
        ]);
    }

    public function edit(Contact $contact): Response
    {
        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
            'processes' => Process::all(),
        ]);
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'source' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'tag' => 'nullable|string',
            'process_id' => 'nullable|exists:processes,id',
            'current_step' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $contact->status;
        $contact->update($validated);

        if ($oldStatus !== $contact->status) {
            ActivityLog::create([
                'contact_id' => $contact->id,
                'type' => 'zmena_stavu',
                'description' => "Status změněn na: {$contact->status->label()}",
            ]);
        }

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Kontakt byl úspěšně upraven.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Kontakt byl smazán.');
    }

    public function addActivity(Request $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
        ]);

        ActivityLog::create([
            'contact_id' => $contact->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'Aktivita byla přidána.');
    }

    public function assignProcess(Request $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'process_id' => 'required|exists:processes,id',
        ]);

        $process = Process::with('steps')->findOrFail($validated['process_id']);
        $firstStep = $process->steps->first();

        $contact->update([
            'process_id' => $process->id,
            'current_step' => $firstStep?->order ?? 1,
        ]);

        if ($firstStep) {
            $contact->tasks()->create([
                'step_id' => $firstStep->id,
                'title' => $firstStep->name,
                'due_date' => now()->addDays($firstStep->duration_days),
                'priority' => 'stredni',
                'is_auto' => true,
            ]);
        }

        ActivityLog::create([
            'contact_id' => $contact->id,
            'type' => 'proces',
            'description' => "Přiřazen do procesu: {$process->name}",
        ]);

        return back()->with('success', "Kontakt přiřazen do procesu {$process->name}.");
    }

    public function advanceStep(Contact $contact): RedirectResponse
    {
        if (!$contact->process_id || !$contact->current_step) {
            return back()->with('error', 'Kontakt není přiřazen k žádnému procesu.');
        }

        $process = $contact->process()->with('steps')->first();
        $currentStep = $process->steps->where('order', $contact->current_step)->first();
        $nextStep = $process->steps->where('order', '>', $contact->current_step)->first();

        // Mark current tasks for this step as done
        $contact->tasks()->where('step_id', $currentStep?->id)->where('is_done', false)->update(['is_done' => true]);

        ActivityLog::create([
            'contact_id' => $contact->id,
            'type' => 'krok_dokoncen',
            'description' => "Krok dokončen: {$currentStep?->name}",
        ]);

        if ($nextStep) {
            $contact->update(['current_step' => $nextStep->order]);

            $contact->tasks()->create([
                'step_id' => $nextStep->id,
                'title' => $nextStep->name,
                'due_date' => now()->addDays($nextStep->duration_days),
                'priority' => 'stredni',
                'is_auto' => true,
            ]);

            return back()->with('success', "Posunuto na krok: {$nextStep->name}");
        }

        // Process completed
        $contact->update(['status' => 'uzavreno', 'tag' => 'uzavreno']);

        ActivityLog::create([
            'contact_id' => $contact->id,
            'type' => 'proces_dokoncen',
            'description' => "Proces {$process->name} dokončen.",
        ]);

        return back()->with('success', "Proces {$process->name} byl dokončen!");
    }
}
