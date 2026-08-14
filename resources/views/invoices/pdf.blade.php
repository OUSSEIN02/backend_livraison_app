<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoiceNumber }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: right; margin-bottom: 30px; }
        .company-info { margin-bottom: 30px; }
        .invoice-details { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; text-align: left; padding: 10px; border-bottom: 2px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .total { text-align: right; margin-top: 20px; font-size: 16px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <h1>FACTURE</h1>
        <p>N° : {{ $invoiceNumber }}</p>
        <p>Date : {{ \Carbon\Carbon::parse($issuedAt)->format('d/m/Y') }}</p>
    </div>

    <div class="company-info">
        <strong>Émetteur :</strong><br>
        Mon Entreprise de Livraison<br>
        Libreville, Gabon<br>
        contact@monentreprise.ga
    </div>

    <div class="invoice-details">
        <strong>Facturé à :</strong><br>
        Client : {{ $order->dropoff_name ?? 'Client' }}<br>
        Adresse de livraison : {{ $order->dropoff_address ?? 'Non spécifiée' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Service de livraison (Réf: {{ $order->reference }})<br>
                    <small>Départ: {{ $order->pickup_address }}</small><br>
                    <small>Arrivée: {{ $order->dropoff_address }}</small>
                </td>
                <td style="text-align: right;">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        Total TTC : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
    </div>

    <div class="footer">
        <p>Merci pour votre confiance. Ceci est une facture générée automatiquement.</p>
    </div>

</body>
</html>