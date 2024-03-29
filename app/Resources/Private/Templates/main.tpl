<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MHN-Aufnahmetool: {$html_title|default}</title>

    <link rel="icon" href="/favicon.png">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-toggle.min.css" rel="stylesheet">
{if empty($isEmbedded)}<link href="/css/sidebar.css" rel="stylesheet">{/if}
    <link href="/css/MHN.css?v1" rel="stylesheet">
  </head>
  <body id="mhn" {if !empty($isEmbedded)}class="embedded"{/if}>
  {if empty($isEmbedded)}{include file="menue.tpl"}{/if}
    <div class="main"><div class="container-fluid">
        <h1>{$titel|default}</h1>

        {include file=$innentemplate}

        <hr />
    </div></div>

    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  {if empty($isEmbedded)}<script src="/js/sidebar.js"></script>{/if}
    <script src="/js/bootstrap-toggle.min.js"></script>
    {if !empty($isEmbedded)}<script src="/js/resize-iframe.js" type="module"></script>{/if}
  </body>
</html>
