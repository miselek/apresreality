<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\Process;
use App\Models\Task;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $nabory = Process::where('name', 'Nábory')->first();
        $prodeje = Process::where('name', 'Prodeje')->first();
        $dotahovacky = Process::where('name', 'Dotahovačky')->first();

        $contacts = [
            // === Prodávající (vlastníci nemovitostí) ===
            [
                'name' => 'Ing. Pavel Kratochvíl',
                'phone' => '+420 603 412 789',
                'email' => 'kratochvil@email.cz',
                'source' => 'doporuceni',
                'type' => 'prodavajici',
                'status' => 'aktivni',
                'tag' => 'horka',
                'notes' => 'Doporučení od Josefa Muzikáře. Prodej bytu na Smíchově, spěchá.',
                'process_id' => $nabory?->id,
                'current_step' => 3,
            ],
            [
                'name' => 'MUDr. Helena Dvořáčková',
                'phone' => '+420 724 555 321',
                'email' => 'dvorackova@seznam.cz',
                'source' => 'vlastni_web',
                'type' => 'prodavajici',
                'status' => 'aktivni',
                'tag' => 'horka',
                'notes' => 'Přišla přes web. Prodej RD v Černošicích, zastupuje Kuba.',
                'process_id' => $nabory?->id,
                'current_step' => 4,
            ],
            [
                'name' => 'Tomáš Marek',
                'phone' => '+420 775 200 100',
                'email' => 'marek.tomas@centrum.cz',
                'source' => 'sreality',
                'type' => 'prodavajici',
                'status' => 'aktivni',
                'tag' => 'ok',
                'notes' => 'Stavební pozemek v Hostivici. Zpracovává Ondřej Chaloupek.',
                'process_id' => $nabory?->id,
                'current_step' => 1,
            ],
            [
                'name' => 'JUDr. Martin Šťastný',
                'phone' => '+420 602 888 444',
                'email' => 'stastny@akstastny.cz',
                'source' => 'doporuceni',
                'type' => 'prodavajici',
                'status' => 'aktivni',
                'tag' => 'horka',
                'notes' => 'Prodej komerčního prostoru na Vinohradech. Právní zázemí vyřešeno. Koordinuje Josef.',
                'process_id' => $prodeje?->id,
                'current_step' => 4,
            ],

            // === Kupci ===
            [
                'name' => 'Bc. Tereza Nováková',
                'phone' => '+420 731 222 333',
                'email' => 'novakova.t@gmail.com',
                'source' => 'sreality',
                'type' => 'kupec',
                'status' => 'aktivni',
                'tag' => 'horka',
                'notes' => 'Hledá 3+kk v Praze 5. Hypotéka schválena. Prohlídka dnes!',
                'process_id' => $prodeje?->id,
                'current_step' => 3,
            ],
            [
                'name' => 'Ing. David Procházka',
                'phone' => '+420 608 777 999',
                'email' => 'prochazka.d@icloud.com',
                'source' => 'socialni_site',
                'type' => 'kupec',
                'status' => 'aktivni',
                'tag' => 'ok',
                'notes' => 'Zájem o RD, rozpočet 10-14M. Přišel přes Instagram kampaň.',
                'process_id' => $prodeje?->id,
                'current_step' => 2,
            ],
            [
                'name' => 'PhDr. Klára Benešová',
                'phone' => '+420 777 444 222',
                'email' => 'benesova@volny.cz',
                'source' => 'doporuceni',
                'type' => 'kupec',
                'status' => 'aktivni',
                'tag' => 'horka',
                'notes' => 'Doporučení od Kratochvílů. Chce byt na Smíchově. Řeší Jan Jakubík.',
                'process_id' => $prodeje?->id,
                'current_step' => 3,
            ],
            [
                'name' => 'Marek Horák',
                'phone' => '+420 734 111 888',
                'email' => 'horak.m@firma.cz',
                'source' => 'fermakleri',
                'type' => 'kupec',
                'status' => 'ceka',
                'tag' => 'studena',
                'notes' => 'Poptávka přes Fermakleri. Nemá schválenou hypotéku, čeká na banku.',
            ],

            // === Investoři ===
            [
                'name' => 'Ing. Robert Černý, MBA',
                'phone' => '+420 602 333 777',
                'email' => 'cerny@investcapital.cz',
                'source' => 'doporuceni',
                'type' => 'investor',
                'status' => 'aktivni',
                'tag' => 'horka',
                'notes' => 'Portfolio investor, hledá komerční prostory. Koordinuje Josef Muzikář.',
                'process_id' => $dotahovacky?->id,
                'current_step' => 3,
            ],
            [
                'name' => 'Petr Fiala',
                'phone' => '+420 736 999 555',
                'email' => 'fiala.petr@seznam.cz',
                'source' => 'socialni_site',
                'type' => 'investor',
                'status' => 'aktivni',
                'tag' => 'ok',
                'notes' => 'Hledá investiční byt k pronájmu, Praha 2-5. Budget do 5M.',
            ],

            // === Nájemníci ===
            [
                'name' => 'Simona Králová',
                'phone' => '+420 601 456 789',
                'email' => 'kralova.s@email.cz',
                'source' => 'vlastni_web',
                'type' => 'najemnik',
                'status' => 'aktivni',
                'tag' => 'ok',
                'notes' => 'Hledá pronájem 2+kk, Praha 3-10. Budget 18-22k/měs.',
            ],

            // === Uzavřeno ===
            [
                'name' => 'Mgr. Radka Veselá',
                'phone' => '+420 777 888 999',
                'email' => 'vesela.r@gmail.com',
                'source' => 'doporuceni',
                'type' => 'kupec',
                'status' => 'uzavreno',
                'tag' => 'uzavreno',
                'notes' => 'Koupila byt 2+1, Vinohrady. Úspěšný obchod, Kuba + Ondřej.',
                'process_id' => $prodeje?->id,
                'current_step' => 6,
            ],
        ];

        foreach ($contacts as $data) {
            $contact = Contact::create($data);

            ActivityLog::create([
                'contact_id' => $contact->id,
                'type' => 'poznamka',
                'description' => 'Kontakt vytvořen v systému',
            ]);

            if ($contact->process_id) {
                ActivityLog::create([
                    'contact_id' => $contact->id,
                    'type' => 'proces',
                    'description' => 'Přiřazen do procesu: ' . $contact->process?->name,
                ]);
            }

            // Create tasks for contacts in a process
            if ($contact->process_id && $contact->current_step) {
                $currentStepModel = $contact->process?->steps()
                    ->where('order', $contact->current_step)
                    ->first();

                if ($currentStepModel) {
                    Task::create([
                        'contact_id' => $contact->id,
                        'step_id' => $currentStepModel->id,
                        'title' => $currentStepModel->name,
                        'due_date' => now()->addDays($currentStepModel->duration_days),
                        'priority' => 'stredni',
                        'is_done' => false,
                        'is_auto' => true,
                    ]);
                }
            }
        }

        // === Extra manual tasks for presentation ===
        $kratochvil = Contact::where('name', 'like', '%Kratochvíl%')->first();
        $novakova = Contact::where('name', 'like', '%Nováková%')->first();
        $dvorackova = Contact::where('name', 'like', '%Dvořáčková%')->first();
        $cerny = Contact::where('name', 'like', '%Černý%')->first();
        $benesova = Contact::where('name', 'like', '%Benešová%')->first();
        $prochazka = Contact::where('name', 'like', '%Procházka%')->first();

        $manualTasks = [
            ['contact_id' => $novakova?->id, 'title' => 'Prohlídka bytu Smíchov s p. Novákovou', 'due_date' => now(), 'priority' => 'vysoka'],
            ['contact_id' => $kratochvil?->id, 'title' => 'Připravit cenovou analýzu - byt Smíchov', 'due_date' => now(), 'priority' => 'vysoka'],
            ['contact_id' => $dvorackova?->id, 'title' => 'Objednat fotografa - RD Černošice', 'due_date' => now()->addDay(), 'priority' => 'stredni'],
            ['contact_id' => $cerny?->id, 'title' => 'Odeslat nabídku komerčních prostor', 'due_date' => now()->addDay(), 'priority' => 'vysoka'],
            ['contact_id' => $benesova?->id, 'title' => 'Domluvit druhou prohlídku - Benešová', 'due_date' => now()->addDays(2), 'priority' => 'stredni'],
            ['contact_id' => $prochazka?->id, 'title' => 'Ověřit stav hypotéky - Procházka', 'due_date' => now()->addDays(2), 'priority' => 'nizka'],
            ['contact_id' => $kratochvil?->id, 'title' => 'Konzultace strategie prodeje', 'due_date' => now()->addDays(3), 'priority' => 'stredni'],
            ['contact_id' => $novakova?->id, 'title' => 'Follow-up po prohlídce', 'due_date' => now()->addDays(1), 'priority' => 'vysoka'],
        ];

        foreach ($manualTasks as $taskData) {
            if ($taskData['contact_id']) {
                Task::create(array_merge($taskData, [
                    'is_done' => false,
                    'is_auto' => false,
                ]));
            }
        }
    }
}
