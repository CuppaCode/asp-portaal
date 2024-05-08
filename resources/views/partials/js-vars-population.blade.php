@php

 $canAssignCompany = 0;

 if(auth()->user()->can('assign_company')) {

    $canAssignCompany = 1;

 }

@endphp

<script type="text/javascript">

    var canAssignCompany = {{ $canAssignCompany }};

</script>