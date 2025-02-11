<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Damaged Part</span> <span class="badge badge-primary">Schade aard</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-damaged-part") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_damaged_part">Old Damaged Part</label>
                    <select class="form-control select2" name="old_damaged_part" id="old_damaged_part" required>
                        @foreach($claimDamagedParts as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_damaged_part') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_damaged_part">New Damage Part</label>
                    <select class="form-control select2" name="new_damaged_part" id="new_damaged_part" required>
                        @foreach($claimDamagedParts as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_damaged_part') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateDamagedPartClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateDamagedPartClaimsSA[]" id="migrateDamagedPartClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateDamagedPartClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noDamagedPartClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noDamagedPartClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateDamagedPartClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateDamagedPartClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>