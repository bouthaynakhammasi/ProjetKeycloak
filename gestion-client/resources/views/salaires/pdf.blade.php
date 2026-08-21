<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche de Paie - {{ $salaire->employe->nom_complet }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-section h3 {
            background-color: #f4f4f4;
            padding: 10px;
            margin: 0 0 15px 0;
            border-left: 4px solid #007bff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            padding: 5px 0;
        }
        .info-item label {
            font-weight: bold;
            color: #555;
        }
        .salary-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .salary-details th,
        .salary-details td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .salary-details th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .salary-details tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #e8f4fd !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-paye {
            background-color: #28a745;
            color: white;
        }
        .status-en_attente {
            background-color: #ffc107;
            color: #333;
        }
        .status-annule {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FICHE DE PAIE</h1>
        <p>Entreprise de Gestion - Système de Paie</p>
        <p>Émis le : {{ date('d/m/Y') }}</p>
    </div>

    <div class="info-section">
        <h3>Informations Employé</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Nom complet :</label>
                {{ $salaire->employe->nom_complet }}
            </div>
            <div class="info-item">
                <label>Email :</label>
                {{ $salaire->employe->email }}
            </div>
            <div class="info-item">
                <label>Poste :</label>
                {{ $salaire->employe->poste }}
            </div>
            <div class="info-item">
                <label>Département :</label>
                {{ $salaire->employe->departement }}
            </div>
        </div>
    </div>

    <div class="info-section">
        <h3>Période de Paie</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>Mois :</label>
                {{ $salaire->nom_mois }}
            </div>
            <div class="info-item">
                <label>Année :</label>
                {{ $salaire->annee }}
            </div>
            <div class="info-item">
                <label>Statut :</label>
                <span class="status-badge status-{{ $salaire->statut_paiement }}">
                    {{ ucfirst($salaire->statut_paiement) }}
                </span>
            </div>
            <div class="info-item">
                <label>Date de paiement :</label>
                {{ $salaire->date_paiement ? $salaire->date_paiement->format('d/m/Y') : 'Non payé' }}
            </div>
        </div>
    </div>

    <div class="info-section">
        <h3>Détails du Salaire</h3>
        <table class="salary-details">
            <thead>
                <tr>
                    <th>Élément</th>
                    <th>Montant (DT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salaire de base</td>
                    <td>{{ number_format($salaire->salaire_base, 2, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>Primes</td>
                    <td>{{ number_format($salaire->prime, 2, ',', ' ') }}</td>
                </tr>
                <tr>
                    <td>Retenues</td>
                    <td>-{{ number_format($salaire->retenue, 2, ',', ' ') }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>SALAIRE NET</strong></td>
                    <td><strong>{{ number_format($salaire->salaire_net, 2, ',', ' ') }} DT</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($salaire->notes)
    <div class="info-section">
        <h3>Notes</h3>
        <p>{{ $salaire->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Ce document est généré automatiquement par le système de gestion de paie.</p>
        <p>Pour toute question, contactez le service des ressources humaines.</p>
        <p>Document généré le : {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
