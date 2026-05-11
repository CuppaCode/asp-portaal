<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificaat Verlengd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .renewal-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="renewal-container">
            <div class="text-center mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h2 class="mt-3 text-success">Certificaat Succesvol Verlengd!</h2>
            </div>

            <div class="alert alert-success">
                <h5><i class="fas fa-check"></i> Succes</h5>
                <p class="mb-0">
                    Het certificaat is succesvol verlengd. U ontvangt zo dadelijk een bevestigingsmail.
                </p>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Nieuwe Gegevens</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 200px;">Certificaat:</th>
                            <td>{{ $certificate->name }}</td>
                        </tr>
                        <tr>
                            <th>Categorie:</th>
                            <td>{{ $certificate->category->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Oude vervaldatum:</th>
                            <td><span class="badge bg-secondary">{{ \Carbon\Carbon::parse($certificate->original_expiry_date)->format('d-m-Y') }}</span></td>
                        </tr>
                        <tr>
                            <th>Nieuwe vervaldatum:</th>
                            <td><span class="badge bg-success">{{ $certificate->expiry_date->format('d-m-Y') }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted">
                    Bedankt voor het verlengen van uw certificaat.<br>
                    U kunt deze pagina nu sluiten.
                </p>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
