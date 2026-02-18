@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.certificateCategory.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.certificate-categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.certificateCategory.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="duration">{{ trans('cruds.certificateCategory.fields.duration') }}</label>
                <input class="form-control {{ $errors->has('duration') ? 'is-invalid' : '' }}" type="number" name="duration" id="duration" value="{{ old('duration', 12) }}">
                @if($errors->has('duration'))
                    <div class="invalid-feedback">{{ $errors->first('duration') }}</div>
                @endif
                <small class="form-text text-muted">Duur van het certificaat in maanden</small>
            </div>

            <hr class="my-4">
            <h5>Notificatie Instellingen</h5>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="enable_notifications" id="enable_notifications" value="1" {{ old('enable_notifications', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="enable_notifications">
                        Notificaties inschakelen
                    </label>
                </div>
                <small class="form-text text-muted">Automatische emails versturen wanneer certificaten bijna verlopen</small>
            </div>

            <div class="form-group">
                <label for="notify_days_before">Aantal dagen van tevoren waarschuwen</label>
                <input class="form-control {{ $errors->has('notify_days_before') ? 'is-invalid' : '' }}" type="number" name="notify_days_before" id="notify_days_before" value="{{ old('notify_days_before', 30) }}" min="1" max="365">
                @if($errors->has('notify_days_before'))
                    <div class="invalid-feedback">{{ $errors->first('notify_days_before') }}</div>
                @endif
                <small class="form-text text-muted">Hoeveel dagen voor de vervaldatum moet de eerste notificatie verstuurd worden</small>
            </div>

            <div class="form-group">
                <label for="reminder_frequency_days">Herinnering elke X dagen</label>
                <input class="form-control {{ $errors->has('reminder_frequency_days') ? 'is-invalid' : '' }}" type="number" name="reminder_frequency_days" id="reminder_frequency_days" value="{{ old('reminder_frequency_days', 7) }}" min="1" max="30">
                @if($errors->has('reminder_frequency_days'))
                    <div class="invalid-feedback">{{ $errors->first('reminder_frequency_days') }}</div>
                @endif
                <small class="form-text text-muted">Als certificaat niet verlengd is, elke hoeveel dagen een herinnering versturen</small>
            </div>

            <div class="form-group">
                <label class="required" for="notification_recipients">Admin Email Adressen</label>
                <input class="form-control {{ $errors->has('notification_recipients') ? 'is-invalid' : '' }}" type="text" name="notification_recipients" id="notification_recipients" value="{{ old('notification_recipients', '') }}" required>
                @if($errors->has('notification_recipients'))
                    <div class="invalid-feedback">{{ $errors->first('notification_recipients') }}</div>
                @endif
                <small class="form-text text-muted">Email adressen gescheiden door komma's (bijv: admin@bedrijf.nl, manager@bedrijf.nl)</small>
                <div id="email-warning" class="text-warning mt-2" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> Let op: er zijn geen email adressen ingevuld
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Email validation and warning
        function validateEmails() {
            const input = $('#notification_recipients').val().trim();
            const enableNotifications = $('#enable_notifications').is(':checked');
            
            if (enableNotifications && !input) {
                $('#email-warning').show();
            } else {
                $('#email-warning').hide();
            }
            
            // Split by comma and validate each email
            if (input) {
                const emails = input.split(',').map(e => e.trim());
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const invalidEmails = emails.filter(email => email && !emailRegex.test(email));
                
                if (invalidEmails.length > 0) {
                    $('#notification_recipients').addClass('is-invalid');
                    if ($('#notification_recipients').next('.invalid-feedback').length === 0) {
                        $('#notification_recipients').after('<div class="invalid-feedback">Ongeldige email adressen: ' + invalidEmails.join(', ') + '</div>');
                    }
                } else {
                    $('#notification_recipients').removeClass('is-invalid');
                    $('#notification_recipients').next('.invalid-feedback').remove();
                }
            }
        }
        
        $('#notification_recipients, #enable_notifications').on('input change', validateEmails);
        validateEmails();
    });
</script>
@endsection
