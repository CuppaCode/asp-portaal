@if (Session::has('message'))

    <div class="alert alert-success position-fixed custom-alert" role="alert">
        {{ Session::get('message') }}
    </div>
 
@endif

<div class="alert alert-success position-fixed custom-alert" id="js-message" role="alert">
</div>