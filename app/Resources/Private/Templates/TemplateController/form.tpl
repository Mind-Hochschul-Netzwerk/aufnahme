<h2>Vorlage bearbeiten: {$template->getLabel()|escape}</h2>

<form action="/templates/" method="post">
    <input type="hidden" name="templateName" value="{$template->getName()|escape}">

    {if $template->getHints()}
        <div class="row">
            <div class='col-sm-2'>Verwendbare Variablen:</div>
            <div class="col-sm-10"><pre>{$template->getHints()}</pre></div>
        </div>
    {/if}

    <div class="row">
        <label for='input-subject' class='col-sm-2 col-form-label'>Betreff</label>
        <div class="col-sm-10"><input id="input-subject" name="subject" class="form-control" value="{$template->getSubject()|escape}"></div>
    </div>

    <div class="row">
        <label for='input-text' class='col-sm-2 col-form-label'>Inhalt</label>
        <div class="col-sm-10"><textarea id="input-text" name="text" class="form-control" rows="20">{$template->getText()|escape}</textarea></div>
    </div>

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Speichern" />
        </div>
    </div>
</form>