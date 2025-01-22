<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Damage Origin</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-damage-origin") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_damage_origin">Old Damage Origin</label>
                    <select class="form-control select2" name="old_damage_origin" id="old_damage_origin" required>
                        @foreach($claimDamageOrigin as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_damage_origin') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_damage_origin">New Damage Origin</label>
                    <select class="form-control select2" name="new_damage_origin" id="new_damage_origin" required>
                        @foreach($claimDamageOrigin as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_damage_origin') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateDamageOriginClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateDamageOriginClaimsSA[]" id="migrateDamageOriginClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateDamageOriginClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        <i class="fa-fw fas fa-cogs mr-1">
        
                        </i>
                        GO! 
                    </button>
                    <br/>
                    <small>Please handle with care!</small>
                </div>
        
            </form>

            @if (Session::has('noDamageOriginClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noDamageOriginClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateDamageOriginClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateDamageOriginClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>