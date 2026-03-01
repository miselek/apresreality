<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        private SmsSluzbaService $smsService,
    ) {}

    public function renderTemplate(NotificationTemplate $template, Contact $contact, array $extra = []): array
    {
        $variables = [
            '{{jmeno}}' => $contact->name,
            '{{telefon}}' => $contact->phone ?? '',
            '{{email}}' => $contact->email ?? '',
            '{{datum}}' => now()->format('d.m.Y'),
            ...$extra,
        ];

        $body = str_replace(array_keys($variables), array_values($variables), $template->body);
        $subject = $template->subject
            ? str_replace(array_keys($variables), array_values($variables), $template->subject)
            : null;

        return ['subject' => $subject, 'body' => $body];
    }

    public function sendSms(Contact $contact, NotificationTemplate $template, array $extra = []): bool
    {
        if (!$contact->phone) {
            return false;
        }

        $rendered = $this->renderTemplate($template, $contact, $extra);
        return $this->smsService->send($contact->phone, $rendered['body']);
    }

    public function sendEmail(Contact $contact, NotificationTemplate $template, array $extra = []): bool
    {
        if (!$contact->email) {
            return false;
        }

        $rendered = $this->renderTemplate($template, $contact, $extra);

        try {
            Mail::raw($rendered['body'], function ($message) use ($contact, $rendered) {
                $message->to($contact->email)
                    ->subject($rendered['subject'] ?? 'Apres Reality');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
