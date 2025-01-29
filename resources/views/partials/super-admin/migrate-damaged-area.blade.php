<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Damaged Area</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-damaged-area") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_damaged_area">Old Damaged Area</label>
                    <select class="form-control select2" name="old_damaged_area" id="old_damaged_area" required>
                        @foreach($claimDamagedArea as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_damaged_area') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_damaged_area">New Damaged Area</label>
                    <select class="form-control select2" name="new_damaged_area" id="new_damaged_area" required>
                        @foreach($claimDamagedArea as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_damaged_area') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateDamagedAreaClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateDamagedAreaClaimsSA[]" id="migrateDamagedAreaClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateDamagedAreaClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noDamagedAreaClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noDamagedAreaClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateDamagedAreaClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateDamagedAreaClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>