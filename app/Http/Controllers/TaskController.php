<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tasks/Index', [
            'overdue' => Task::with('contact')->overdue()->orderBy('due_date')->get(),
            'today' => Task::with('contact')->today()->orderByRaw("CASE priority WHEN 'vysoka' THEN 1 WHEN 'stredni' THEN 2 WHEN 'nizka' THEN 3 END")->get(),
            'tomorrow' => Task::with('contact')->tomorrow()->orderBy('due_date')->get(),
            'upcoming' => Task::with('contact')->upcoming()->orderBy('due_date')->limit(30)->get(),
            'done' => Task::with('contact')->done()->latest('updated_at')->limit(20)->get(),
            'contacts' => Contact::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'due_date' => 'required|date',
            'priority' => 'required|string',
        ]);

        Task::create([
            ...$validated,
            'is_auto' => false,
        ]);

        return back()->with('success', 'Úkol byl vytvořen.');
    }

    public function complete(Task $task): RedirectResponse
    {
        $task->update(['is_done' => true]);

        if ($task->contact_id) {
            ActivityLog::create([
                'contact_id' => $task->contact_id,
                'type' => 'ukol_dokoncen',
                'description' => "Úkol dokončen: {$task->title}",
            ]);
        }

        return back()->with('success', 'Úkol byl označen jako hotový.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('success', 'Úkol byl smazán.');
    }
}
