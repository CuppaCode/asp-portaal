<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Damaged Area Opposite</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-damaged-area-opposite") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_damaged_area_opposite">Old Damaged Area Opposite</label>
                    <select class="form-control select2" name="old_damaged_area_opposite" id="old_damaged_area_opposite" required>
                        @foreach($claimDamagedAreaOpposite as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_damaged_area_opposite') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_damaged_area_opposite">New Damaged Area Opposite</label>
                    <select class="form-control select2" name="new_damaged_area_opposite" id="new_damaged_area_opposite" required>
                        @foreach($claimDamagedAreaOpposite as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_damaged_area_opposite') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateDamagedAreaOppositeClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateDamagedAreaOppositeClaimsSA[]" id="migrateDamagedAreaOppositeClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateDamagedAreaOppositeClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noDamagedAreaOppositeClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noDamagedAreaOppositeClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateDamagedAreaOppositeClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateDamagedAreaOppositeClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>