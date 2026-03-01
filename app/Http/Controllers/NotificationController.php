<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Notifications/Index', [
            'templates' => NotificationTemplate::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Notifications/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sms,email',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        NotificationTemplate::create($validated);

        return redirect()->route('notifications.index')
            ->with('success', 'Šablona byla vytvořena.');
    }

    public function edit(NotificationTemplate $notification): Response
    {
        return Inertia::render('Notifications/Edit', [
            'template' => $notification,
        ]);
    }

    public function update(Request $request, NotificationTemplate $notification): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sms,email',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        $notification->update($validated);

        return redirect()->route('notifications.index')
            ->with('success', 'Šablona byla upravena.');
    }

    public function destroy(NotificationTemplate $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Šablona byla smazána.');
    }

    public function preview(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:notification_templates,id',
            'contact_id' => 'required|exists:contacts,id',
            'extra' => 'nullable|array',
        ]);

        $template = NotificationTemplate::findOrFail($validated['template_id']);
        $contact = Contact::findOrFail($validated['contact_id']);
        $service = app(NotificationService::class);

        $rendered = $service->renderTemplate($template, $contact, $validated['extra'] ?? []);

        return response()->json($rendered);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:notification_templates,id',
            'contact_id' => 'required|exists:contacts,id',
            'extra' => 'nullable|array',
        ]);

        $template = NotificationTemplate::findOrFail($validated['template_id']);
        $contact = Contact::findOrFail($validated['contact_id']);
        $service = app(NotificationService::class);

        $success = $template->type === 'sms'
            ? $service->sendSms($contact, $template, $validated['extra'] ?? [])
            : $service->sendEmail($contact, $template, $validated['extra'] ?? []);

        if ($success) {
            ActivityLog::create([
                'contact_id' => $contact->id,
                'type' => $template->type === 'sms' ? 'sms' : 'email',
                'description' => "Odesláno: {$template->name}",
            ]);
            return back()->with('success', 'Zpráva byla odeslána.');
        }

        return back()->with('error', 'Zprávu se nepodařilo odeslat. Zkontrolujte nastavení.');
    }
}
