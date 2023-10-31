@php 

    $isAdmin = auth()->user()->roles->contains(1);

@endphp

<script type="text/javascript">

    var isAdmin = {{ $isAdmin }};

</script>