<h2>E-Mail</h2>

<form action="{$self}" method="post">

    <div class="row">
        <div class="col-sm-2">Von</div>
        <div class="col-sm-10">{$absende_email_kand|escape}</div>
    </div>

    <div class="row">
        <div class="col-sm-2">An</div>
        <div class="col-sm-10">{$antrag->getEMail()|escape}</div>
    </div>

    <div class='form-group row '>
        <label for='input-betreff' class='col-sm-2 col-form-label'>Betreff</label>
        <div class='col-sm-10'><input id='input-betreff' name='betreff' class='form-control' value="{$mailSubject|escape}"></div>
    </div>

    <div class='form-group row '>
        <label for='input-mailtext' class='col-sm-2 col-form-label'>Inhalt</label>
        <div class="col-sm-10">
        <textarea class="form-control" rows="20" id="input-mailtext" name="mailtext">{$mailText|escape}</textarea>
        </div>
    </div>

    <input type="hidden" name="formular" value="{$aktion}" />

    <div class='row'>
        <div class='col-sm-offset-2 col-sm-10'>
            <input class="btn btn-success" type="submit" value="Bestätigen" />
        </div>
    </div>
</form>
