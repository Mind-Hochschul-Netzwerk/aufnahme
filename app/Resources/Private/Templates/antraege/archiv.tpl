<h1>Entschiedene Anträge</h1>

<ul>
<li>Angenommene Anträge werden zwei Wochen aufbewahrt (für den Fall, dass Fehler aufgetreten sind) und dann gelöscht.</li>
<li>Abgelehnte Anträge werden 60 Wochen aufbewahrt (da dem Bewerber/der Bewerberin Berufung bei der nächsten Mitgliederversammlung zusteht) und dann gelöscht.</li>
</ul>

<table border="1" cellpadding="3" cellspacing="0">
    {foreach from=$antraege key=index item=a}
        {if $index % 20 === 0}
            <tr><th>Nr.</th><th>Antragssteller</th><th>Entscheidungsdatum</th><th>Status</th><th>Voten</th><th>Bemerkung</th></tr>
        {/if}
        <tr>
            <td><a href="/antraege/{$a->getID()}/">{$a->getID()}</a></td>
            <td>{$a->getName()|escape}</td>
            <td>{$a->getDatumEntscheidung()}</td>
            <td>{$a->getStatusReadable()}</td>
            <td>
                {foreach from=$a->getVotes() item=v}
                    {$v->getUsername()|escape}: {$v->getValueReadable()}<br />
                {/foreach}
            </td>
            <td>{$a->getBemerkung()|escape}</td>
        </tr>
    {/foreach}
</table>
