<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        NotificationTemplate::create([
            'name' => 'Potvrzení prohlídky',
            'type' => 'sms',
            'body' => 'Dobrý den {{jmeno}}, potvrzujeme prohlídku nemovitosti na adrese {{adresa}} dne {{datum}}. Těšíme se na setkání. Kuba Kořínek, Apres Reality',
        ]);

        NotificationTemplate::create([
            'name' => 'Připomenutí schůzky',
            'type' => 'sms',
            'body' => 'Dobrý den {{jmeno}}, připomínáme zítřejší schůzku ({{datum}}). V případě změny mě prosím kontaktujte. Kuba Kořínek',
        ]);

        NotificationTemplate::create([
            'name' => 'Follow-up po schůzce',
            'type' => 'email',
            'subject' => 'Děkuji za schůzku — {{adresa}}',
            'body' => "Dobrý den {{jmeno}},\n\nděkuji za dnešní schůzku ohledně nemovitosti na adrese {{adresa}}.\n\nNa základě naší diskuze připravím cenovou analýzu a ozvu se Vám do 2 pracovních dnů s výsledky.\n\nPokud máte jakékoliv dotazy, neváhejte mě kontaktovat.\n\nS pozdravem,\nKuba Kořínek\nApres Reality",
        ]);

        NotificationTemplate::create([
            'name' => 'Urgence nájmu — 1. upomínka',
            'type' => 'sms',
            'body' => 'Dobrý den {{jmeno}}, evidujeme nezaplacenou platbu nájmu ve výši {{castka}} Kč se splatností {{datum}}. Prosíme o úhradu. Děkujeme, Apres Reality',
        ]);

        NotificationTemplate::create([
            'name' => 'Urgence nájmu — 2. upomínka',
            'type' => 'email',
            'subject' => 'Druhá upomínka — nezaplacený nájem',
            'body' => "Dobrý den {{jmeno}},\n\nposíláme druhou upomínku ohledně nezaplacené platby nájmu ve výši {{castka}} Kč se splatností {{datum}}.\n\nProsíme o okamžitou úhradu. V případě dotazů nás kontaktujte.\n\nS pozdravem,\nApres Reality",
        ]);

        NotificationTemplate::create([
            'name' => 'Žádost o referenci',
            'type' => 'email',
            'subject' => 'Jak se Vám bydlí? — Apres Reality',
            'body' => "Dobrý den {{jmeno}},\n\nuplynul měsíc od předání nemovitosti na adrese {{adresa}} a rádi bychom se zeptali, jak jste spokojeni.\n\nPokud jste s našimi službami spokojeni, budeme rádi za Vaši referenci nebo doporučení.\n\nDěkujeme a přejeme vše dobré,\nKuba Kořínek\nApres Reality",
        ]);
    }
}
