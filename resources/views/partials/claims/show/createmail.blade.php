<div class="tab-pane pt-3" id="mailSection" role="tabpanel" aria-labelledby="mail-tab">
    <form method="POST" action="{{ route('admin.claims.sendMail') }}"
        enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <div class="form-group">
                <label class="required" for="mailReceiver">Ontvanger</label>
                <select class="form-control select2" name="mailReceiver[]" id="mailReceiver" required multiple="multiple">

                    @foreach ($allContactsInCompany as $id => $entry)
                        <option value="{{ $entry->email }}"
                            {{ old('mailReceiver') ? 'selected' : '' }}>
                            {{ $entry->first_name ?? '' }} {{ $entry->last_name ?? '' }} -
                            {{ $entry->email }}</option>
                    @endforeach
                </select>

            </div>
            <div class="form-group">
                <label for="mailCc">CC</label>
                <select class="form-control select2" name="mailCc[]" id="mailCc" multiple="multiple">

                    @foreach ($allContactsInCompany as $id => $entry)
                        <option value="{{ $entry->email }}"
                            {{ old('mailCc') ? 'selected' : '' }}>
                            {{ $entry->first_name ?? '' }} {{ $entry->last_name ?? '' }} -
                            {{ $entry->email }}</option>
                    @endforeach
                </select>

            </div>

            <div class="form-group">
                <label for="mailBcc">BCC</label>
                <select class="form-control select2" name="mailBcc[]" id="mailBcc" multiple="multiple">

                    @foreach ($allContactsInCompany as $id => $entry)
                        <option value="{{ $entry->email }}"
                            {{ old('mailBcc') ? 'selected' : '' }}>
                            {{ $entry->first_name ?? '' }} {{ $entry->last_name ?? '' }} -
                            {{ $entry->email }}</option>
                    @endforeach
                </select>

            </div>


            <div class="form-group">
                <label for="template">Template</label>
                <select class="form-control select2" name="mailTemplate" id="mailTemplate">

                    <option selected disabled>{{ trans('global.pleaseSelect') }}</option>

                    @foreach ($mailTemplates as $id => $entry)
                        <option value="{{ $entry->body }}"
                            data-subject="{{ $entry->subject ?? '' }}">{{ $entry->name ?? '' }}
                        </option>
                    @endforeach
                </select>

            </div>
            <div class="form-group">

                <label for="mailSubject" class="required">Onderwerp</label>
                <input type="text" class="form-control" name="mailSubject" id="mailSubject"
                    value="" required>

            </div>
            
            <div class="form-group">

              <label class="required" for="mailBody">Bericht</label>
              <textarea class="form-control" name="mailBody" id="mailBody">{!! old('mailBody') !!}</textarea>
              
            </div>
            
            <div class="form-group">

                <label for="mailAttachments">Bijlage</label>
                <input type="file" name="mailAttachments[]" id="mailAttachments" multiple>

            </div>
            

            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            <div class="form-group d-none">
                <select class="form-control select2 {{ $errors->has('claims') ? 'is-invalid' : '' }}"
                    name="claims[]" id="claims" multiple required>
                    <option value="{{ $claim->id }}" selected>{{ $claim->id }}</option>
                </select>
            </div>
            <div class="d-none" id="claimJson">{{ json_encode($claim) }}</div>

            @if ($firstContact)
                <div class="d-none" id="contactJson">{{ json_encode($firstContact) }}</div>
            @endif

            @if (isset($claim->recovery_office))
                @php

                    $recoveryOffice = App\Models\Company::find($claim->recovery_office->company_id);

                @endphp

                <div class="d-none" id="recoveryJson">{{ json_encode($recoveryOffice) }}</div>

                @if (!$recoveryOffice->contacts->isEmpty())
                    <div class="d-none" id="recoveryContactJson">
                        {{ json_encode($recoveryOffice->contacts) }}</div>
                @endif
            @endif

            @if ($driver)

                <div class="d-none" id="driverJson">
                    {{ json_encode($driver) }}</div> 

            @endif

            @if ($oppositeVehicleInfo)

                <div class="d-none" id="oppositeJson">
                    {{ json_encode($oppositeVehicleInfo) }}</div>

            @endif
        

            <div class="d-none" id="statusSelectJson">
                {{ json_encode(App\Models\Claim::STATUS_SELECT) }}</div>
            <div class="d-none" id="damagePartSelectJson">
                {{ json_encode(App\Models\Claim::DAMAGED_PART_SELECT) }}</div>
            <div class="d-none" id="damageAreaSelectJson">
                {{ json_encode(App\Models\Claim::DAMAGED_AREA_SELECT) }}</div>
            <div class="d-none" id="damageOriginJson">
                {{ json_encode(App\Models\Claim::DAMAGE_ORIGIN) }}</div>
            <div class="d-none" id="damageKindJson">
                {{ json_encode(App\Models\Claim::DAMAGE_KIND) }}</div>
            <div class="d-none" id="recoverableClaimJson">
                {{ json_encode(App\Models\Claim::RECOVERABLE_CLAIM_SELECT) }}</div>

        </div>
        <div class="form-group">
            <button class="btn btn-danger" type="submit" name="add-task-dashboard" value='true'>
                {{ trans('global.send') }}
            </button>
        </div>
    </form>