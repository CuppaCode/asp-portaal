  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white" id="exampleModalLabel" data-modal-title>...</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" data-modal-body>
          ...
        </div>
        <div class="modal-body">
          
          <div class="form-group">
            <select class="form-control select2" name="decline_reason" id="decline_reason" required>

                @foreach (App\Models\Claim::DECLINE_REASON_SELECT as $key => $decline_reason)
                        
                  <option value="{{ $key }}">{{ $decline_reason }}</option>

                @endforeach
                
            </select>
            <input type="hidden" name="claim_id" value="{{ $claim->id }}"/>
          </div>

        </div>

        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
          <button type="button" class="btn btn-danger" data-modal-save>Afwijzen</button>
        </div>
      </div>
    </div>
  </div>