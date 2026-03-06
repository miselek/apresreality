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

        // Property 1: Byt in inzerce (Jakub Kořínek zastupuje)
        $p1 = Property::create([
            'name' => 'Byt 3+kk, Praha 5 - Smíchov',
            'address' => 'Plzeňská 123',
            'city' => 'Praha 5',
            'zip' => '150 00',
            'gps_lat' => 50.0755,
            'gps_lng' => 14.3978,
            'property_type' => 'byt',
            'disposition' => '3+kk',
            'area' => 85,
            'price' => 6500000,
            'price_type' => 'prodej',
            'commission_percent' => 3,
            'ad_budget' => 50000,
            'ad_spent' => 15000,
            'description' => 'Krásný světlý byt v 3. patře cihlového domu s výhledem na Vltavu. Nová kuchyňská linka, parkety po renovaci. Dva balkóny, sklep. Zastupující makléř: Jakub Kořínek.',
            'status' => 'inzerce',
            'contact_id' => $contacts->where('name', 'like', '%Kratochvíl%')->first()?->id,
            'published_at' => Carbon::now()->subDays(14),
            'notes' => 'Exkluzivní zastoupení. Makléř: Jakub Kořínek. Focení proběhlo 20.2., inzerce spuštěna 21.2.',
        ]);

        // Property 2: Dům in prohlídky (Josef Muzikář)
        $p2 = Property::create([
            'name' => 'Rodinný dům, Černošice',
            'address' => 'K Lesíku 456',
            'city' => 'Černošice',
            'zip' => '252 28',
            'gps_lat' => 49.9612,
            'gps_lng' => 14.3272,
            'property_type' => 'dum',
            'disposition' => '5+1',
            'area' => 180,
            'land_area' => 650,
            'price' => 12800000,
            'price_type' => 'prodej',
            'commission_percent' => 4,
            'ad_budget' => 80000,
            'ad_spent' => 35000,
            'description' => 'Prostorný rodinný dům v klidné lokalitě. Zahrada s bazénem, dvojgaráž, nová střecha. Vhodné pro rodinu s dětmi. Zastupující makléř: Josef Muzikář.',
            'status' => 'prohlidky',
            'contact_id' => $contacts->where('name', 'like', '%Dvořáčková%')->first()?->id,
            'published_at' => Carbon::now()->subDays(30),
            'notes' => 'Makléř: Josef Muzikář. 3 prohlídky proběhly, 2 zájemci. Čekáme na rozhodnutí.',
        ]);

        // Property 3: Pozemek in nábor (Ondřej Chaloupek)
        $p3 = Property::create([
            'name' => 'Stavební pozemek, Hostivice',
            'address' => 'Na Palouku',
            'city' => 'Hostivice',
            'zip' => '253 01',
            'gps_lat' => 50.0781,
            'gps_lng' => 14.2581,
            'property_type' => 'pozemek',
            'land_area' => 800,
            'price' => 3200000,
            'price_type' => 'prodej',
            'commission_percent' => 5,
            'ad_budget' => 30000,
            'description' => 'Rovinatý stavební pozemek s IS na hranici pozemku. Klidná lokalita, dobrá dostupnost do Prahy. Zastupující makléř: Ondřej Chaloupek.',
            'status' => 'nabor',
            'contact_id' => $contacts->where('name', 'like', '%Marek%')->first()?->id,
            'notes' => 'Makléř: Ondřej Chaloupek. Čeká se na podpis zprostředkovatelské smlouvy.',
        ]);

        // Property 4: Komerční in rezervace (Jan Jakubík)
        $p4 = Property::create([
            'name' => 'Komerční prostor, Praha 2',
            'address' => 'Vinohradská 789',
            'city' => 'Praha 2',
            'zip' => '120 00',
            'gps_lat' => 50.0755,
            'gps_lng' => 14.4378,
            'property_type' => 'komercni',
            'area' => 120,
            'price' => 15000000,
            'price_type' => 'prodej',
            'commission_percent' => 3.5,
            'ad_budget' => 100000,
            'ad_spent' => 85000,
            'description' => 'Reprezentativní obchodní prostor v přízemí na frekventované ulici. Vysoké stropy, velké výlohy. Zastupující makléř: Jan Jakubík.',
            'status' => 'rezervace',
            'contact_id' => $contacts->where('name', 'like', '%Šťastný%')->first()?->id,
            'published_at' => Carbon::now()->subDays(45),
            'notes' => 'Makléř: Jan Jakubík. Rezervační smlouva podepsána 3.3. Investor: Ing. Robert Černý.',
        ]);

        // Property 5: Byt v přípravě (Jakub Kořínek)
        $p5 = Property::create([
            'name' => 'Byt 2+1, Vinohrady',
            'address' => 'Mánesova 42',
            'city' => 'Praha 2',
            'zip' => '120 00',
            'gps_lat' => 50.0775,
            'gps_lng' => 14.4350,
            'property_type' => 'byt',
            'disposition' => '2+1',
            'area' => 62,
            'price' => 5200000,
            'price_type' => 'prodej',
            'commission_percent' => 3,
            'ad_budget' => 40000,
            'description' => 'Zrekonstruovaný byt ve vyhledávané lokalitě Vinohrad. Nové rozvody, podlahové topení. Zastupující makléř: Jakub Kořínek.',
            'status' => 'priprava',
            'contact_id' => $contacts->where('name', 'like', '%Veselá%')->first()?->id,
            'notes' => 'Makléř: Jakub Kořínek. Čeká se na focení a přípravu podkladů.',
        ]);

        // Add interests
        $novakova = $contacts->where('name', 'like', '%Nováková%')->first();
        $benesova = $contacts->where('name', 'like', '%Benešová%')->first();
        $prochazka = $contacts->where('name', 'like', '%Procházka%')->first();
        $cerny = $contacts->where('name', 'like', '%Černý%')->first();
        $fiala = $contacts->where('name', 'like', '%Fiala%')->first();
        $horak = $contacts->where('name', 'like', '%Horák%')->first();

        // Byt Smíchov — zájemci
        if ($p1->id && $novakova) {
            PropertyInterest::create(['property_id' => $p1->id, 'contact_id' => $novakova->id, 'type' => 'zajemce', 'note' => 'Velký zájem, hypotéka schválena. Prohlídka dnes.']);
        }
        if ($p1->id && $benesova) {
            PropertyInterest::create(['property_id' => $p1->id, 'contact_id' => $benesova->id, 'type' => 'navsteva', 'visited_at' => Carbon::now()->subDays(3), 'note' => 'Prohlídka proběhla, zájem o další jednání.']);
        }
        if ($p1->id && $horak) {
            PropertyInterest::create(['property_id' => $p1->id, 'contact_id' => $horak->id, 'type' => 'zajemce', 'note' => 'Čeká na schválení hypotéky.']);
        }

        // RD Černošice — zájemci
        if ($p2->id && $prochazka) {
            PropertyInterest::create(['property_id' => $p2->id, 'contact_id' => $prochazka->id, 'type' => 'navsteva', 'visited_at' => Carbon::now()->subDays(7), 'note' => 'Líbilo se, ale řeší rozpočet.']);
        }
        if ($p2->id && $novakova) {
            PropertyInterest::create(['property_id' => $p2->id, 'contact_id' => $novakova->id, 'type' => 'zajemce', 'note' => 'Zvažuje i dům místo bytu.']);
        }

        // Komerční Vinohrady — investor
        if ($p4->id && $cerny) {
            PropertyInterest::create(['property_id' => $p4->id, 'contact_id' => $cerny->id, 'type' => 'rezervace', 'note' => 'Rezervační smlouva podepsána 3.3. Kauce přijata.']);
        }

        // Byt Vinohrady — zájem
        if ($p5->id && $fiala) {
            PropertyInterest::create(['property_id' => $p5->id, 'contact_id' => $fiala->id, 'type' => 'zajemce', 'note' => 'Investiční byt k pronájmu, chce vidět po focení.']);
        }

        // === Events (dnes + blízká budoucnost) ===

        // Dnes: prohlídka bytu Smíchov
        if ($p1->id) {
            PropertyEvent::create([
                'property_id' => $p1->id,
                'contact_id' => $novakova?->id,
                'title' => 'Prohlídka s p. Novákovou',
                'type' => 'prohlidka',
                'starts_at' => Carbon::today()->addHours(15),
                'ends_at' => Carbon::today()->addHours(16),
                'location' => 'Plzeňská 123, Praha 5',
                'notes' => 'Makléř Jakub Kořínek. Klientka má schválenou hypotéku.',
            ]);
        }

        // Dnes: schůzka s vlastníkem pozemku
        if ($p3->id) {
            PropertyEvent::create([
                'property_id' => $p3->id,
                'contact_id' => $contacts->where('name', 'like', '%Marek%')->first()?->id,
                'title' => 'Schůzka s vlastníkem - pozemek',
                'type' => 'schuzka',
                'starts_at' => Carbon::today()->addHours(10),
                'ends_at' => Carbon::today()->addHours(11),
                'location' => 'Kancelář Apres Reality',
                'notes' => 'Makléř Ondřej Chaloupek. Podpis zprostředkovatelské smlouvy.',
            ]);
        }

        // Zítra: focení RD Černošice
        if ($p2->id) {
            PropertyEvent::create([
                'property_id' => $p2->id,
                'title' => 'Focení interiéru + drony',
                'type' => 'foceni',
                'starts_at' => Carbon::tomorrow()->addHours(10),
                'ends_at' => Carbon::tomorrow()->addHours(13),
                'location' => 'K Lesíku 456, Černošice',
                'notes' => 'Fotograf Tomáš Novotný. Koordinuje Josef Muzikář.',
            ]);
        }

        // Za 2 dny: druhá prohlídka RD
        if ($p2->id) {
            PropertyEvent::create([
                'property_id' => $p2->id,
                'contact_id' => $prochazka?->id,
                'title' => 'Druhá prohlídka - Procházka',
                'type' => 'prohlidka',
                'starts_at' => Carbon::today()->addDays(2)->addHours(16),
                'location' => 'K Lesíku 456, Černošice',
                'notes' => 'Makléř Josef Muzikář. Klient chce přijít s manželkou.',
            ]);
        }

        // Za 3 dny: schůzka k podpisu kupní smlouvy
        if ($p4->id) {
            PropertyEvent::create([
                'property_id' => $p4->id,
                'contact_id' => $cerny?->id,
                'title' => 'Podpis kupní smlouvy - komerční prostor',
                'type' => 'schuzka',
                'starts_at' => Carbon::today()->addDays(3)->addHours(14),
                'ends_at' => Carbon::today()->addDays(3)->addHours(15)->addMinutes(30),
                'location' => 'Advokátní kancelář JUDr. Šťastný',
                'notes' => 'Makléř Jan Jakubík. Kupující: Ing. Robert Černý, MBA.',
            ]);
        }

        // Minulá událost (dokončená)
        if ($p1->id) {
            PropertyEvent::create([
                'property_id' => $p1->id,
                'contact_id' => $benesova?->id,
                'title' => 'Prohlídka s p. Benešovou',
                'type' => 'prohlidka',
                'starts_at' => Carbon::now()->subDays(3)->addHours(14),
                'ends_at' => Carbon::now()->subDays(3)->addHours(15),
                'location' => 'Plzeňská 123, Praha 5',
                'is_completed' => true,
                'notes' => 'Prohlídka proběhla, klientka má zájem o další jednání.',
            ]);
        }
    }
}
