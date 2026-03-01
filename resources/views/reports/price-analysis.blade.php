<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Cenová analýza — Apres Reality</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #D4A843; padding-bottom: 20px; }
        .header h1 { color: #0F172A; font-size: 24px; margin: 0; }
        .header p { color: #666; margin: 5px 0 0; }
        .price-box { background: #FFF9E6; border: 2px solid #D4A843; border-radius: 8px; text-align: center; padding: 20px; margin: 20px 0; }
        .price-box .label { color: #D4A843; font-size: 14px; }
        .price-box .value { color: #0F172A; font-size: 28px; font-weight: bold; margin: 5px 0; }
        .price-box .per-m2 { color: #B8922F; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #E5E7EB; }
        th { background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase; }
        .footer { text-align: center; color: #9CA3AF; font-size: 10px; margin-top: 40px; border-top: 1px solid #E5E7EB; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Apres Reality</h1>
        <p>Cenová analýza nemovitosti</p>
    </div>

    <div class="price-box">
        <div class="label">Odhadní tržní cena</div>
        <div class="value">
            {{ $analysis->estimated_price ? number_format($analysis->estimated_price, 0, ',', ' ') . ' Kč' : 'Nebylo stanoveno' }}
        </div>
        @if($analysis->estimated_price && $analysis->area)
            <div class="per-m2">
                {{ number_format($analysis->estimated_price / $analysis->area, 0, ',', ' ') }} Kč / m²
            </div>
        @endif
    </div>

    <h2 style="color: #0F172A; font-size: 16px; margin-top: 30px;">Údaje o nemovitosti</h2>
    <table>
        <tr><th>Adresa</th><td>{{ $analysis->address }}</td></tr>
        <tr><th>Typ</th><td>{{ $analysis->property_type }}</td></tr>
        <tr><th>Plocha</th><td>{{ $analysis->area }} m²</td></tr>
        <tr><th>Stav</th><td>{{ $analysis->condition }}</td></tr>
        @if($analysis->floor)<tr><th>Patro</th><td>{{ $analysis->floor }}</td></tr>@endif
        @if($analysis->ownership)<tr><th>Vlastnictví</th><td>{{ $analysis->ownership }}</td></tr>@endif
    </table>

    @if($analysis->contact)
        <h2 style="color: #0F172A; font-size: 16px; margin-top: 30px;">Kontakt</h2>
        <table>
            <tr><th>Jméno</th><td>{{ $analysis->contact->name }}</td></tr>
            @if($analysis->contact->phone)<tr><th>Telefon</th><td>{{ $analysis->contact->phone }}</td></tr>@endif
            @if($analysis->contact->email)<tr><th>E-mail</th><td>{{ $analysis->contact->email }}</td></tr>@endif
        </table>
    @endif

    <div class="footer">
        <p>Tento dokument je orientačním odhadem a nepředstavuje závaznou nabídku.</p>
        <p>Apres Reality — Kuba Kořínek | Vygenerováno {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
