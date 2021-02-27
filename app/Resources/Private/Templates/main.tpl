<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MHN-Aufnahmetool: {$html_title|default}</title>

    <!-- META -->

    <link rel="icon" href="./favicon.png">

    <!-- CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="/css/sidebar.css" rel="stylesheet">
    <link href="/css/MHN.css?<?=md5((string)filemtime('/var/www/html/css/MHN.css'))?>" rel="stylesheet">

    <?=$htmlHead?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.min.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    {include file="menue.tpl"}
    <div class="main"><div class="container-fluid">
        <h1>{$titel|default}</h1>

        {include file=$innentemplate}

        <hr />
    </div></div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/sidebar.js"></script>
    <script src="js/bootstrap-toggle.min.js"></script>
    <script src="js/MHN.js"></script>
  </body>
</html>
