<?php

namespace Database\Seeders;

use App\Models\Process;
use App\Models\Step;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    public function run(): void
    {
        // Proces A: Nábory
        $nabory = Process::create([
            'name' => 'Nábory',
            'color' => '#8B5CF6',
            'badge' => 'A',
            'note' => 'Získání zakázky od prodávajícího',
        ]);
        $this->createSteps($nabory, [
            ['order' => 1, 'name' => 'První kontakt / zaznamenání poptávky', 'duration_days' => 0],
            ['order' => 2, 'name' => 'Příprava cenové analýzy', 'description' => 'Zpracování tržního odhadu ceny nemovitosti', 'duration_days' => 2],
            ['order' => 3, 'name' => 'Schůzka s majitelem', 'duration_days' => 1],
            ['order' => 4, 'name' => 'Personalizovaný e-mail po schůzce', 'description' => 'Shrnutí schůzky a dalších kroků', 'duration_days' => 1],
            ['order' => 5, 'name' => 'Podpis zprostředkovatelské smlouvy', 'duration_days' => 2],
        ]);

        // Proces B: Prodeje
        $prodeje = Process::create([
            'name' => 'Prodeje',
            'color' => '#3B82F6',
            'badge' => 'B',
            'note' => 'Aktivní prodej nemovitosti',
        ]);
        $this->createSteps($prodeje, [
            ['order' => 1, 'name' => 'Objednání fotografa + homestaging', 'duration_days' => 3],
            ['order' => 2, 'name' => 'Příprava inzerátního textu', 'description' => 'AI návrh textu inzerátu', 'duration_days' => 1],
            ['order' => 3, 'name' => 'Publikace na portálech', 'duration_days' => 1],
            ['order' => 4, 'name' => 'Prohlídky se zájemci', 'duration_days' => 14],
            ['order' => 5, 'name' => 'Vyhodnocení nabídek', 'duration_days' => 2],
            ['order' => 6, 'name' => 'Rezervační smlouva', 'duration_days' => 2],
        ]);

        // Proces C: Dotahovačky
        $dotahovacky = Process::create([
            'name' => 'Dotahovačky',
            'color' => '#EF4444',
            'badge' => 'C',
            'note' => 'Po rezervaci — administrativní kroky až do předání',
        ]);
        $this->createSteps($dotahovacky, [
            ['order' => 1, 'name' => 'Příprava kupní smlouvy', 'duration_days' => 5],
            ['order' => 2, 'name' => 'Schválení hypotéky kupujícího', 'duration_days' => 21],
            ['order' => 3, 'name' => 'Podpis KS + notář', 'duration_days' => 2],
            ['order' => 4, 'name' => 'Podání návrhu na katastr', 'duration_days' => 1],
            ['order' => 5, 'name' => 'Zápis v katastru (hlídání stavu)', 'duration_days' => 30],
            ['order' => 6, 'name' => 'Předávací protokol + klíče', 'duration_days' => 1],
            ['order' => 7, 'name' => 'Žádost o referenci', 'description' => 'Automatický e-mail po 7 dnech od předání', 'duration_days' => 7, 'is_auto' => true],
        ]);

        // Proces D: Správa nájmu
        $najem = Process::create([
            'name' => 'Správa nájmu',
            'color' => '#10B981',
            'badge' => 'D',
            'note' => 'Měsíční cyklus kontroly plateb',
        ]);
        $this->createSteps($najem, [
            ['order' => 1, 'name' => 'Kontrola přijaté platby', 'duration_days' => 0, 'is_auto' => true],
            ['order' => 2, 'name' => '1. urgence (D+3)', 'description' => 'Pokud nezaplaceno, odeslat první upomínku', 'duration_days' => 3, 'is_auto' => true],
            ['order' => 3, 'name' => '2. urgence (D+7)', 'description' => 'Druhá upomínka nájemníkovi', 'duration_days' => 4, 'is_auto' => true],
            ['order' => 4, 'name' => 'Notifikace makléře k přímému řešení (D+10)', 'description' => 'Manuální kontaktování nájemníka', 'duration_days' => 3],
        ]);
    }

    private function createSteps(Process $process, array $steps): void
    {
        foreach ($steps as $step) {
            Step::create([
                'process_id' => $process->id,
                'order' => $step['order'],
                'name' => $step['name'],
                'description' => $step['description'] ?? null,
                'duration_days' => $step['duration_days'],
                'is_auto' => $step['is_auto'] ?? false,
            ]);
        }
    }
}
