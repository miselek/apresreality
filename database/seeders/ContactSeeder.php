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
            [
                'name' => 'Martin Novák',
                'phone' => '+420 773 456 789',
                'email' => 'novak@gmail.com',
                'source' => 'sreality',
                'type' => 'kupec',
                'status' => 'aktivni',
                'tag' => 'horka',
                'process_id' => $prodeje?->id,
                'current_step' => 3,
            ],
            [
                'name' => 'Lucie Horáková',
                'phone' => '+420 603 222 111',
                'email' => 'horakova@seznam.cz',
                'source' => 'doporuceni',
                'type' => 'prodavajici',
                'status' => 'aktivni',
                'tag' => 'horka',
                'process_id' => $nabory?->id,
                'current_step' => 2,
            ],
            [
                'name' => 'Petr Kovář',
                'phone' => '+420 734 888 000',
                'email' => 'kovar@firma.cz',
                'source' => 'fermakleri',
                'type' => 'najemnik',
                'status' => 'aktivni',
                'tag' => 'ok',
            ],
            [
                'name' => 'Jana Poláková',
                'phone' => '+420 601 555 333',
                'email' => 'polakova@email.cz',
                'source' => 'socialni_site',
                'type' => 'kupec',
                'status' => 'ceka',
                'tag' => 'studena',
            ],
            [
                'name' => 'Tomáš Blažek',
                'phone' => '+420 777 100 200',
                'email' => 'blazek@podnik.cz',
                'source' => 'vlastni_web',
                'type' => 'investor',
                'status' => 'uzavreno',
                'tag' => 'uzavreno',
                'process_id' => $dotahovacky?->id,
                'current_step' => 7,
            ],
            [
                'name' => 'Eva Marková',
                'phone' => '+420 731 200 300',
                'email' => 'markova@volny.cz',
                'source' => 'doporuceni',
                'type' => 'prodavajici',
                'status' => 'aktivni',
                'tag' => 'horka',
                'process_id' => $nabory?->id,
                'current_step' => 4,
            ],
            [
                'name' => 'Jiří Pospíšil',
                'phone' => '+420 608 777 888',
                'email' => 'pospisil@icloud.com',
                'source' => 'sreality',
                'type' => 'kupec',
                'status' => 'aktivni',
                'tag' => 'ok',
                'process_id' => $prodeje?->id,
                'current_step' => 1,
            ],
            [
                'name' => 'Marie Černá',
                'phone' => '+420 725 333 444',
                'email' => 'cerna@centrum.cz',
                'source' => 'fermakleri',
                'type' => 'prodavajici',
                'status' => 'ceka',
                'tag' => 'studena',
            ],
            [
                'name' => 'Radek Fiala',
                'phone' => '+420 736 555 666',
                'email' => 'fiala@seznam.cz',
                'source' => 'socialni_site',
                'type' => 'investor',
                'status' => 'aktivni',
                'tag' => 'ok',
            ],
            [
                'name' => 'Kateřina Veselá',
                'phone' => '+420 777 999 000',
                'email' => 'vesela@gmail.com',
                'source' => 'doporuceni',
                'type' => 'kupec',
                'status' => 'uzavreno',
                'tag' => 'uzavreno',
            ],
        ];

        foreach ($contacts as $data) {
            $contact = Contact::create($data);

            // Add some activity logs
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
    }
}
