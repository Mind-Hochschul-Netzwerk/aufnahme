{if !empty($message)}<p><span class="formmeldung">{$message|escape}</span></p>{/if}

<h1>Bitte anmelden</h1>

<form action="{$self}" method="post">
<p style="text-align:center;border:2px solid #555; background:#eee;padding:2em;">
<b>Benutzername:</b><br /><input type="text" name="login" size="20" id="login" /><br />
<b>Passwort:</b><br /><input type="password" name="password" id="password" size="20" /><br /><br />
{if !empty($login_posts)}{foreach from=$login_posts key=k item=i}
<input type="hidden" name="{$k|escape}" value="{$i|escape}" />
{/foreach}{/if}
<input type="submit" value="login" id="blogin" />
</p>
</form>

