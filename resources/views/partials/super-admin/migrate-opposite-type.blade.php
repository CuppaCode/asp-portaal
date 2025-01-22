<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Opposite Type</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-opposite-type") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_opposite_type">Old Opposite Type</label>
                    <select class="form-control select2" name="old_opposite_type" id="old_opposite_type" required>
                        @foreach($claimOppositeTypes as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_opposite_type') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_opposite_type">New Opposite Type</label>
                    <select class="form-control select2" name="new_opposite_type" id="new_opposite_type" required>
                        @foreach($claimOppositeTypes as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_opposite_type') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateOppositeTypeClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateOppositeTypeClaimsSA[]" id="migrateOppositeTypeClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateOppositeTypeClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noOppositeTypeClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noOppositeTypeClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateOppositeTypeClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateOppositeTypeClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>