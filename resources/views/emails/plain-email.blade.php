<div id="header">
    <img src="{{ config('app.url') }}/images/logo-asp.png" width="333" height="55">
</div>
<div id="body">

    {!! nl2br($body) !!}

</div>
<div id="footer">

    <div class="left">

        <img src="{{ config('app.url') }}/images/logo-asp.png" width="333" height="55">

    </div>

    <div class="right">

        <ul>
            <li>            			
                <span><a href="https://www.autoschadeplan.nl" target="_blank">www.autoschadeplan.nl</a></span>
            </li>
        </ul>

    </div>

</div>

<style>

    #header,
    #footer {
        background: #344A9B;
        padding: 20px;
    }

    #body {
        padding: 30px;
    }

    #footer {
        display: flex;
        justify-content: space-between;
    }

    #footer .right ul li a {
        color: white;
        text-decoration: none;
    }

    #footer .right ul li a:hover {
        text-decoration: underline;
    }

    #footer .right ul li {
        list-style-type: none;
    }

</style>