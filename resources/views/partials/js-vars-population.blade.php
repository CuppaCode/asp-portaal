@php

 $isAdminOrAgent = 0;

 if(auth()->user()->isAdminOrAgent()) {

    $isAdminOrAgent = 1;

 }

@endphp

<script type="text/javascript">

    var isAdminOrAgent = {{ $isAdminOrAgent }};

</script>