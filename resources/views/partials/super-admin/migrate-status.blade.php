<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Status</span> (ALPHA)
        </div>

        <div class="card-body">
            
            <form method="POST" action="{{ route("admin.super-admin.migrate-status") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_status">Old status</label>
                    <select class="form-control select2" name="old_status" id="old_status" required>
                        @foreach($claimStatusses as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_status') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_status">New status</label>
                    <select class="form-control select2" name="new_status" id="new_status" required>
                        @foreach($claimStatusses as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_status') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateStatusClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateStatusClaimsSA[]" id="migrateStatusClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateStatusClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noStatusClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noStatusClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateStatusClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateStatusClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif

        </div>
        
    </div>

</div>