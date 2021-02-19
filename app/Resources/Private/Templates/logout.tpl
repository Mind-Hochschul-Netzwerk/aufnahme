{if !empty($entry_angemeldet)}
<div class="login">Angemeldet als {if $entry_username!=''}{$entry_username|escape}{else}[unbekannt]{/if}
<form action="{$self}" method="post">
<input type="hidden" name="ausloggen" value="ja" />
<input type="submit" id="blogout" value="logout" />
</form>
</div>
{/if}
