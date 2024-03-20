@php

 $isAdmin = 0;

 if(auth()->user()->can('financial_access')) {

    $isAdmin = 1;

 }

@endphp

<script type="text/javascript">

    var isAdmin = {{ $isAdmin }};

</script>