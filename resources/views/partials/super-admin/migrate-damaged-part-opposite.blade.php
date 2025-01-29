<div class="col-12 col-lg-4">

    <div class="card">

        <div class="card-header">
            Migrate <span class="badge badge-primary">Damaged Part Opposite</span> (ALPHA)
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route("admin.super-admin.migrate-damaged-part-opposite") }}" enctype="multipart/form-data">

                @csrf
        
                <div class="form-group">
        
                    <label class="required" for="old_damaged_part_opposite">Old Damaged Part Opposite</label>
                    <select class="form-control select2" name="old_damaged_part_opposite" id="old_damaged_part_opposite" required>
                        @foreach($claimDamagedPartsOpposite as $id => $entry)
                            <option value="{{ $id }}" {{ old('old_damaged_part_opposite') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
        
                </div>
        
                <div class="form-group">
        
                    <label class="required" for="new_damaged_part_opposite">New Damage Part Opposite</label>
                    <select class="form-control select2" name="new_damaged_part_opposite" id="new_damaged_part_opposite" required>
                        @foreach($claimDamagedPartsOpposite as $id => $entry)
                            <option value="{{ $id }}" {{ old('new_damaged_part_opposite') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    
                </div>
        
                <div class="form-group">
                    
                    <label class="required" for="migrateDamagedPartOppositeClaimsSA">Claims</label>
                    <select class="form-control select2" name="migrateDamagedPartOppositeClaimsSA[]" id="migrateDamagedPartOppositeClaimsSA" required multiple>
                        @foreach($claims as $id => $entry)
                            <option value="{{ $entry->id }}" {{ old('migrateDamagedPartOppositeClaimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

            @if (Session::has('noDamagedPartOppositeClaimsSA'))

                <div class="alert alert-danger" role="alert">
                    {{ Session::get('noDamagedPartOppositeClaimsSA') }}
                </div>

            @endif
        
            @if (Session::has('migrateDamagedPartOppositeClaims'))
        
                <ul class="list-group">
        
                    <li class="list-group-item">Affected claims</li>
        
                    @foreach(Session::get('migrateDamagedPartOppositeClaims') as $claim)
        
                        <a href="{{ route('admin.claims.show', $claim->id) }}" target="_blank" class="list-group-item list-group-item-action">
                            {{ $claim->claim_number }}
                        </a>
        
                    @endforeach
        
                </ul>
        
            @endif
        </div>
        
    </div>

</div>