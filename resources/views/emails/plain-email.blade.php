<div id="body">

    {!! nl2br($body) !!}

</div>

<style>

    html, body {
        background-color: lightgrey!important;
    }

    #body {
        padding: 30px;
        margin: 0 auto;
        width: 70%;
        background-color: white;
        height: auto;
    }

    #footer {
        display: flex;
        justify-content: space-between;
        padding: 30px;
        margin: 0 auto;
        width: 70%;
    }

    #footer .right ul li a {
        color: black;
        text-decoration: none;
    }

    #footer .right ul li a:hover {
        text-decoration: underline;
    }

    #footer .right ul li {
        list-style-type: none;
    }

</style>