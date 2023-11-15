@php

 $isAdmin = 0;

 if(auth()->user()->roles->contains(1)) {

    $isAdmin = 1;

 }

@endphp

<script type="text/javascript">

    var isAdmin = {{ $isAdmin }};

</script>