@if (Session::has('message'))

    <div class="alert alert-success position-absolute custom-alert" role="alert">
        {{ Session::get('message') }}
    </div>
 
@endif

<div class="alert alert-success position-absolute custom-alert" id="js-message" role="alert">
</div>