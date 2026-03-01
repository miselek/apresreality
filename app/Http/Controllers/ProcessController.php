<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Process;
use App\Models\Step;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProcessController extends Controller
{
    public function index(): Response
    {
        $processes = Process::withCount('contacts')
            ->with('steps')
            ->get();

        return Inertia::render('Processes/Index', [
            'processes' => $processes,
        ]);
    }

    public function show(Process $process): Response
    {
        $process->load('steps');

        $contacts = Contact::where('process_id', $process->id)
            ->with('tasks')
            ->get()
            ->groupBy('current_step');

        return Inertia::render('Processes/Show', [
            'process' => $process,
            'contactsByStep' => $contacts,
        ]);
    }

    public function edit(Process $process): Response
    {
        $process->load('steps');

        return Inertia::render('Processes/Edit', [
            'process' => $process,
        ]);
    }

    public function update(Request $request, Process $process): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'badge' => 'nullable|string|max:5',
            'note' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.id' => 'nullable|integer',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.duration_days' => 'required|integer|min:0',
            'steps.*.is_auto' => 'boolean',
        ]);

        $process->update([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'badge' => $validated['badge'],
            'note' => $validated['note'],
        ]);

        // Get existing step IDs
        $existingStepIds = $process->steps()->pluck('id')->toArray();
        $updatedStepIds = [];

        foreach ($validated['steps'] as $order => $stepData) {
            if (!empty($stepData['id']) && in_array($stepData['id'], $existingStepIds)) {
                // Update existing step
                Step::where('id', $stepData['id'])->update([
                    'order' => $order + 1,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?? null,
                    'duration_days' => $stepData['duration_days'],
                    'is_auto' => $stepData['is_auto'] ?? false,
                ]);
                $updatedStepIds[] = $stepData['id'];
            } else {
                // Create new step
                $step = Step::create([
                    'process_id' => $process->id,
                    'order' => $order + 1,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?? null,
                    'duration_days' => $stepData['duration_days'],
                    'is_auto' => $stepData['is_auto'] ?? false,
                ]);
                $updatedStepIds[] = $step->id;
            }
        }

        // Delete removed steps
        Step::where('process_id', $process->id)
            ->whereNotIn('id', $updatedStepIds)
            ->delete();

        return redirect()->route('processes.show', $process)
            ->with('success', 'Proces byl úspěšně upraven.');
    }
}
