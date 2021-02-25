<h1>MHN-Mitglieder mit Zugriff auf das Aufnahmetool</h1>
</br>
<table border="1" cellpadding="3" cellspacing="0">
<tr><th>Benutzername</th><th>Name</th></tr>
{foreach from=$webusers item=w}
<td>{$w->getUsername()|escape}</td><td>{$w->getRealname()|escape}</td></tr>
{/foreach}
</table>
<p>Für Änderungen wendet euch bitte an die Mitgliederbetreuung.</p>
