<nav class="navbar navbar-mhn sidebar" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-sidebar-navbar-collapse-1">
                <span class="sr-only">Navigation aufklappen</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img src="/img/mhn-logo-small.png" id="mhn-logo"><span class="logo-text"> Aufnahme
            </span>
                <span class='pull-right showopacity glyphicon'><img src="/img/mhn-logo-small.png" id="mhn-icon"></span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-sidebar-navbar-collapse-1">
            <ul class="nav navbar-nav">

{if $menue_vorhanden}
    <li><a href='/antraege/'>Offene Anträge<span class='pull-right showopacity glyphicon glyphicon-book'></span></a></li>
    <li><a href='/entschiedeneAntraege/'>Entschiedene Anträge<span class='pull-right showopacity glyphicon glyphicon-book'></span></a></li>
    <li><a href='/benutzer/'>Aufnahmekommission<span class='pull-right showopacity glyphicon glyphicon-user'></span></a></li>
    <li><a href='/?logout=1'>Logout<span class='pull-right showopacity glyphicon glyphicon-log-out'></span></a></li>
{else}
    <li><a href='https://www.{$DOMAINNAME}'>Startseite <span class='pull-right showopacity glyphicon glyphicon-globe'></span></a></li>
    <li><a href='/'>Login<span class='pull-right showopacity glyphicon glyphicon-log-in'></span></a></li>
    <li><a href='/antrag/'>Mitglied werden<span class='pull-right showopacity glyphicon glyphicon-plus'></span></a></li>
    <li><a href='https://www.{$DOMAINNAME}/mod/page/view.php?id=12'>Datenschutz<span class='pull-right showopacity glyphicon glyphicon-paragraph'></span></a></li>
    <li><a href='https://www.{$DOMAINNAME}/mod/page/view.php?id=5'>Impressum<span class='pull-right showopacity glyphicon glyphicon-globe'></span></a></li>
{/if}


            </ul>
        </div>
    </div>
</nav>
