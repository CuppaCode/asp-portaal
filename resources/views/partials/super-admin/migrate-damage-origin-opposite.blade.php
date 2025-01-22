<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Damage Origin Opposite</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-damage-origin-opposite") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_damage_origin_opposite">Old Damage Origin Opposite</label>
                    <select class="form-control select2" name="old_damage_origin_opposite" id="old_damage_origin_opposite" required>
                        @foreach($claimDamageOriginOpposite as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_damage_origin_opposite') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_damage_origin_opposite">New Damage Origin Opposite</label>
                    <select class="form-control select2" name="new_damage_origin_opposite" id="new_damage_origin_opposite" required>
                        @foreach($claimDamageOriginOpposite as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_damage_origin_opposite') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateDamageOriginOppositeClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateDamageOriginOppositeClaimsSA[]" id="migrateDamageOriginOppositeClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateDamageOriginOppositeClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noDamageOriginOppositeClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noDamageOriginOppositeClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateDamageOriginOppositeClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateDamageOriginOppositeClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>