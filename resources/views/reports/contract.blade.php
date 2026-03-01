<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Smlouva — Apres Reality</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #D4A843; padding-bottom: 20px; }
        .header h1 { color: #0F172A; font-size: 22px; margin: 0; }
        .header p { color: #666; margin: 5px 0 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #E5E7EB; }
        th { background: #F9FAFB; color: #6B7280; font-size: 11px; text-transform: uppercase; width: 30%; }
        .footer { text-align: center; color: #9CA3AF; font-size: 10px; margin-top: 40px; border-top: 1px solid #E5E7EB; padding-top: 15px; }
        .signature { margin-top: 60px; }
        .signature-line { border-top: 1px solid #333; width: 200px; display: inline-block; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Apres Reality</h1>
        <p>{{ $contract->template?->name ?? 'Smlouva' }}</p>
    </div>

    @if($contract->contact)
        <h2 style="color: #0F172A; font-size: 16px; margin-top: 20px;">Smluvní strana</h2>
        <table>
            <tr><th>Jméno</th><td>{{ $contract->contact->name }}</td></tr>
            @if($contract->contact->phone)<tr><th>Telefon</th><td>{{ $contract->contact->phone }}</td></tr>@endif
            @if($contract->contact->email)<tr><th>E-mail</th><td>{{ $contract->contact->email }}</td></tr>@endif
        </table>
    @endif

    @if($contract->data && count($contract->data))
        <h2 style="color: #0F172A; font-size: 16px; margin-top: 30px;">Údaje smlouvy</h2>
        <table>
            @foreach($contract->data as $key => $value)
                <tr><th>{{ $key }}</th><td>{{ $value }}</td></tr>
            @endforeach
        </table>
    @endif

    <div class="signature">
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: center; width: 50%;">
                    <div class="signature-line"></div>
                    <p>Zprostředkovatel</p>
                </td>
                <td style="border: none; text-align: center; width: 50%;">
                    <div class="signature-line"></div>
                    <p>Klient</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Apres Reality — Kuba Kořínek | Vygenerováno {{ now()->format('d.m.Y') }}</p>
    </div>
</body>
</html>
