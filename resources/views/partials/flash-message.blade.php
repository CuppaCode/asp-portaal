@if (Session::has('message'))

    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show">

        <div 
            class="alert alert-success position-fixed custom-alert"
            role="alert">
            {{ Session::get('message') }}
        </div>
    </div>
 
@endif

<div class="alert position-fixed custom-alert" id="js-message" role="alert">
</div>