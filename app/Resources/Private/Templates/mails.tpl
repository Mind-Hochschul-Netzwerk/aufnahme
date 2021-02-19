
{if !empty($fehler)}

<p>Fehler: {$fehler|escape}</p>

{else}
<p><a href="/antraege/{$antrag_id}/#aktionen">Zur&uuml;ck zum Antrag</a></p>
<p>Mail versandt am {$mail.time|date_format:"%d.%m.%Y"} von {$mail.userName|escape} zwecks {$mail.grund|escape}.</p>
<p><strong>Betreff:</strong> {$mail.subject|escape}</p>
<pre>
{$mail.text|escape}
</pre>

{/if}
