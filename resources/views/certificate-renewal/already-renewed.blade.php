<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificaat Al Verlengd</title>
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
                <h2 class="mt-3">Certificaat Al Verlengd</h2>
            </div>

            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Let Op</h5>
                <p class="mb-0">
                    Dit certificaat is al verlengd tot <strong>{{ $certificate->expiry_date->format('d-m-Y') }}</strong>.
                </p>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Certificaat Gegevens</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 200px;">Naam:</th>
                            <td>{{ $certificate->name }}</td>
                        </tr>
                        <tr>
                            <th>Categorie:</th>
                            <td>{{ $certificate->category->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Nieuwe vervaldatum:</th>
                            <td><span class="badge bg-success">{{ $certificate->expiry_date->format('d-m-Y') }}</span></td>
                        </tr>
                        @if($certificate->renewed_by_email)
                        <tr>
                            <th>Verlengd door:</th>
                            <td>{{ $certificate->renewed_by_email }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-muted">
                    Voor vragen kunt u contact opnemen met 
                    @if($certificate->category && $certificate->category->notification_recipients && count($certificate->category->notification_recipients) > 0)
                        <a href="mailto:{{ $certificate->category->notification_recipients[0] }}">
                            {{ $certificate->category->notification_recipients[0] }}
                        </a>
                    @else
                        uw beheerder
                    @endif
                </p>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
