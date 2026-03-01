<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Property;
use App\Models\PropertyInterest;
use App\Models\PropertyEvent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $contacts = Contact::all();

        // Property 1: Byt in inzerce
        $p1 = Property::create([
            'name' => 'Byt 3+kk, Praha 5 - Smíchov',
            'address' => 'Plzeňská 123',
            'city' => 'Praha 5',
            'zip' => '150 00',
            'property_type' => 'byt',
            'disposition' => '3+kk',
            'area' => 85,
            'price' => 6500000,
            'price_type' => 'prodej',
            'commission_percent' => 3,
            'ad_budget' => 50000,
            'ad_spent' => 15000,
            'description' => 'Krásný světlý byt v 3. patře cihlového domu s výhledem na Vltavu. Nová kuchyňská linka, parkety po renovaci. Dva balkóny, sklep.',
            'status' => 'inzerce',
            'contact_id' => $contacts->where('name', 'Lucie Horáková')->first()?->id,
            'published_at' => Carbon::now()->subDays(14),
        ]);

        // Property 2: Dům in prohlídky
        $p2 = Property::create([
            'name' => 'Rodinný dům, Černošice',
            'address' => 'K Lesíku 456',
            'city' => 'Černošice',
            'zip' => '252 28',
            'property_type' => 'dum',
            'disposition' => '5+1',
            'area' => 180,
            'land_area' => 650,
            'price' => 12800000,
            'price_type' => 'prodej',
            'commission_percent' => 4,
            'ad_budget' => 80000,
            'ad_spent' => 35000,
            'description' => 'Prostorný rodinný dům v klidné lokalitě. Zahrada s bazénem, dvojgaráž, nová střecha. Vhodné pro rodinu s dětmi.',
            'status' => 'prohlidky',
            'contact_id' => $contacts->where('name', 'Eva Procházková')->first()?->id,
            'published_at' => Carbon::now()->subDays(30),
        ]);

        // Property 3: Pozemek in nábor
        $p3 = Property::create([
            'name' => 'Stavební pozemek, Hostivice',
            'address' => 'Na Palouku',
            'city' => 'Hostivice',
            'zip' => '253 01',
            'property_type' => 'pozemek',
            'land_area' => 800,
            'price' => 3200000,
            'price_type' => 'prodej',
            'commission_percent' => 5,
            'ad_budget' => 30000,
            'description' => 'Rovinatý stavební pozemek s IS na hranici pozemku. Klidná lokalita, dobrá dostupnost do Prahy.',
            'status' => 'nabor',
            'contact_id' => $contacts->where('name', 'Martin Novák')->first()?->id,
        ]);

        // Property 4: Komerční in rezervace
        $p4 = Property::create([
            'name' => 'Komerční prostor, Praha 2',
            'address' => 'Vinohradská 789',
            'city' => 'Praha 2',
            'zip' => '120 00',
            'property_type' => 'komercni',
            'area' => 120,
            'price' => 15000000,
            'price_type' => 'prodej',
            'commission_percent' => 3.5,
            'ad_budget' => 100000,
            'ad_spent' => 85000,
            'description' => 'Reprezentativní obchodní prostor v přízemí na frekventované ulici. Vysoké stropy, velké výlohy.',
            'status' => 'rezervace',
            'contact_id' => $contacts->where('name', 'David Král')->first()?->id,
            'published_at' => Carbon::now()->subDays(45),
        ]);

        // Add interests
        if ($p1->id) {
            PropertyInterest::create(['property_id' => $p1->id, 'contact_id' => $contacts->where('name', 'Petr Černý')->first()?->id ?? 1, 'type' => 'zajemce', 'note' => 'Volal s dotazem na dispozice']);
            PropertyInterest::create(['property_id' => $p1->id, 'contact_id' => $contacts->where('name', 'Jana Dvořáková')->first()?->id ?? 2, 'type' => 'navsteva', 'visited_at' => Carbon::now()->subDays(3), 'note' => 'Prohlídka proběhla, zájem o další jednání']);
        }

        if ($p2->id) {
            PropertyInterest::create(['property_id' => $p2->id, 'contact_id' => $contacts->where('name', 'Tomáš Veselý')->first()?->id ?? 3, 'type' => 'navsteva', 'visited_at' => Carbon::now()->subDays(7)]);
            PropertyInterest::create(['property_id' => $p2->id, 'contact_id' => $contacts->where('name', 'Markéta Benešová')->first()?->id ?? 4, 'type' => 'zajemce']);
            PropertyInterest::create(['property_id' => $p2->id, 'contact_id' => $contacts->where('name', 'Jakub Fiala')->first()?->id ?? 5, 'type' => 'navsteva', 'visited_at' => Carbon::now()->subDays(2), 'note' => 'Velký zájem, chce přijít znovu']);
        }

        if ($p4->id) {
            PropertyInterest::create(['property_id' => $p4->id, 'contact_id' => $contacts->where('name', 'Tereza Němcová')->first()?->id ?? 6, 'type' => 'rezervace', 'note' => 'Podpis rezervační smlouvy 5.3.']);
        }

        // Add events
        if ($p1->id) {
            PropertyEvent::create([
                'property_id' => $p1->id,
                'contact_id' => $contacts->where('name', 'Petr Černý')->first()?->id,
                'title' => 'Prohlídka s p. Černým',
                'type' => 'prohlidka',
                'starts_at' => Carbon::today()->addHours(14),
                'ends_at' => Carbon::today()->addHours(15),
                'location' => 'Plzeňská 123, Praha 5',
            ]);
        }

        if ($p2->id) {
            PropertyEvent::create([
                'property_id' => $p2->id,
                'title' => 'Focení interiéru',
                'type' => 'foceni',
                'starts_at' => Carbon::tomorrow()->addHours(10),
                'ends_at' => Carbon::tomorrow()->addHours(12),
                'location' => 'K Lesíku 456, Černošice',
            ]);
            PropertyEvent::create([
                'property_id' => $p2->id,
                'contact_id' => $contacts->where('name', 'Jakub Fiala')->first()?->id,
                'title' => 'Druhá prohlídka - Fiala',
                'type' => 'prohlidka',
                'starts_at' => Carbon::today()->addDays(3)->addHours(16),
                'location' => 'K Lesíku 456, Černošice',
            ]);
        }

        if ($p3->id) {
            PropertyEvent::create([
                'property_id' => $p3->id,
                'contact_id' => $contacts->where('name', 'Martin Novák')->first()?->id,
                'title' => 'Schůzka s vlastníkem',
                'type' => 'schuzka',
                'starts_at' => Carbon::today()->addHours(10),
                'ends_at' => Carbon::today()->addHours(11),
                'location' => 'Kancelář Apres Reality',
            ]);
        }
    }
}
