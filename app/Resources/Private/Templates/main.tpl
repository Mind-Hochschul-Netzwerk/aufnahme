<!DOCTYPE html>
<!-- {include file="VERSION.tpl"} -->
<html lang="de">
  <head>
    <meta http-equiv="content-type" content="application/html; charset=utf-8" />
    <meta name="author" content="Jochen Ott" />
    <meta name="generator" content="author" />
    <link rel="stylesheet" type="text/css" href="/screen.css" media="screen" title="Standard" />
    <link rel="stylesheet" type="text/css" href="/print.css" media="print" />
    <link rel="shortcut icon" href="/img/tut.ico" />
    <title>AK-Tool {$html_title|default}</title>
  </head>
  <body>
    <div id="top"><a href="#main-copy" class="doNotDisplay doNotPrint">Direkt zum Inhalt</a></div>
    <div id="header">
      <div class="superHeader">&nbsp;</div>
      <div class="midHeader">
        <h1 class="headerTitle">{$titel|default}</h1>
      </div>
      <div class="subHeader">&nbsp;</div>
    </div>
    
    <div id="side-bar">
     <div class="lighterBackground">
       <div class="doNotDisplay"><hr /><h2>Hauptmenü</h2></div>
       {include file="menue.tpl"}
       {include file="logout.tpl"}
     </div>
    </div>
    
    <div class="doNotDisplay"><hr /></div>
    <div id="main-copy">{include file=$innentemplate}</div>
    
    <div class="doNotDisplay"><hr /></div>
    <div id="footer">
    <div class="left">
        <a href="https://www.mind-hochschul-netzwerk.de/index.php/impressum/">Impressum</a>
    </div>
    <div class="right">
    </div>
   </div>
</body>
</html>
