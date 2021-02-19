{if $modus eq 'uebersicht'}
<h1>Benutzer: Übersicht</h1>
</br>
{if !empty($meldung)}<p><span class="formmeldung">{$meldung|escape}</span></p>{/if}
<table border="1" cellpadding="3" cellspacing="0">
<tr><th>ID</th><th>Benutzername</th><th>Name</th></tr>
{foreach from=$webusers item=w}
<td>{$w->getID()}</td><td><a href="{$self}{$w->getID()}/">{$w->getUsername()|escape}</a></td><td>{$w->getRealname()|escape}</td></tr>
{/foreach}
</table>

<h2>Neuen Benutzer anlegen</h2>
<p>&nbsp;</p>
<form method="post" action="{$self}">
<table border="0">
<tr><td>Name:</td><td> <input type="text" name="realname" size="20" /></td></tr>
<tr><td>Benutzername:</td><td> <input type="text" name="name" size="20" /></td></tr>
<tr><td>Passwort:</td><td><input type="password" name="passwort" size="20" /></td></tr>
<tr><td>Passwort (wdh.):</td><td><input type="password" name="passwort2" size="20" /></td></tr>
</table>
<input type="hidden" name="formular" value="neu" />
<input type="submit" value="neu anlegen" />
</form>


{elseif $modus eq 'webuser'}
<h1>Benutzer {$webuser->getUsername()|escape}</h1>
{if !empty($meldung)}<p><span class="formmeldung">{$meldung|escape}</span></p>{/if}
<p>&nbsp;</p>
<a href="/benutzer/">zurück zur Übersicht</a>
<h2>Benutzerdaten ändern</h2>
<form action="{$self}" method="post">
<input type="hidden" name="formular" value="webuser" />
<table border="0">
<tr><td>Name:</td><td> <input type="text" name="realname" size="20" value="{$webuser->getRealname()|escape}" /></td></tr>
<tr><td>Benutzername:</td><td><input type="text" name="name" size="20" value="{$webuser->getUsername()|escape}" /></td></tr>
<tr><td>Passwort:</td><td><input type="password" name="passwort" size="20" /></td></tr>
<tr><td>Passwort (wdh.):</td><td><input type="password" name="passwort2" size="20" /></td></tr>
</table>
<input type="submit" value="Ändern" />
</form>


<h2>Benutzer löschen</h2>
{if !empty($loeschen_erlaubt)}
<form action="{$self}" method="post">
<input type="hidden" name="formular" value="loeschen" />
<input type="checkbox" name="wirklich" value="ja" />wirklich
<input type="submit" value="Löschen" />
</form>
{else}
<p>Löschen nicht möglich: man kann nicht den Benutzer löschen, als der man gerade angemeldet ist.</p>
{/if}

{elseif $modus eq 'nichtgefunden'}
<h1>Nicht gefunden</h1>
<p>
Unter der angegebene Adresse wurde nichts gefunden. <a href="/benutzer/">Zur Benutzer-Übersichts-Seite</a>
</p>

{else}
unbekannter Modus (interner Fehler)
{/if}
