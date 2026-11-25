<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificaat Verlengen</title>
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
            <h2 class="mb-4 text-center">
                <i class="fas fa-certificate text-primary"></i> Certificaat Verlengen
            </h2>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
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
                            <th>Chauffeur:</th>
                            <td>{{ $certificate->driver->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Huidige vervaldatum:</th>
                            <td><span class="badge bg-danger">{{ $certificate->expiry_date->format('d-m-Y') }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('certificate.renew.process', $certificate->renewal_token) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="new_expiry_date" class="form-label">Nieuwe Vervaldatum *</label>
                    <input type="date" 
                           class="form-control @error('new_expiry_date') is-invalid @enderror" 
                           id="new_expiry_date" 
                           name="new_expiry_date" 
                           min="{{ $certificate->expiry_date->addDay()->format('Y-m-d') }}"
                           value="{{ old('new_expiry_date') }}"
                           required>
                    @error('new_expiry_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        De nieuwe vervaldatum moet later zijn dan {{ $certificate->expiry_date->format('d-m-Y') }}
                    </small>
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label">Uw Email Adres *</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $certificate->driver->email ?? '') }}"
                           placeholder="uw@email.nl"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Voor bevestiging en administratieve doeleinden
                    </small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check"></i> Certificaat Verlengen
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    Na het verlengen ontvangt u een bevestigingsmail.
                </small>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
