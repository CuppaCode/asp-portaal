@extends('partials.super-admin.tool-template')

@section('space')
6
@endsection

@section('header')
    Migrate Status (ALPHA)
@endsection

@section('body')

    <form method="POST" action="{{ route("admin.super-admin.migrate-status") }}" enctype="multipart/form-data">

        @csrf

        <div class="form-group">

            <label class="required" for="old_status">Old status</label>
            <select class="form-control select2" name="old_status" id="old_status" required>
                @foreach($ClaimStatusses as $id => $entry)
                    <option value="{{ $id }}" {{ old('old_status') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                @endforeach
            </select>

        </div>

        <div class="form-group">

            <label class="required" for="new_status">New status</label>
            <select class="form-control select2" name="new_status" id="new_status" required>
                @foreach($ClaimStatusses as $id => $entry)
                    <option value="{{ $id }}" {{ old('new_status') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                @endforeach
            </select>
            
        </div>

        <div class="form-group">
            
            <label class="required" for="claimsSA">Claims</label>
            <select class="form-control select2" name="claimsSA[]" id="claimsSA" required multiple>
                @foreach($claims as $id => $entry)
                    <option value="{{ $entry->id }}" {{ old('claimsSA') == $entry->id ? 'selected' : '' }}>{{ $entry->claim_number }}</option>
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

    {{-- @if (Session::has('claims'))

        <ul class="list-group">

            @foreach(Session::get('claims') as $claim)

                <li class="list-group-item">{{ $claim->claim_number }}</li>

            @endforeach

        </ul>

    @endif --}}

@endsection