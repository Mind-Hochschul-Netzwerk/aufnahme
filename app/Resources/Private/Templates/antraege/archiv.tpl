<h1>Entschiedene Anträge</h1>

<p>Aus Datenschutzgründen werden die Anträge gelöscht, wenn Sie nicht mehr benötigt werden.</p>

<ul>
    <li>Angenommene Anträge werden zwei Wochen lang aufbewahrt, damit ggf. Fehler festgestellt werden können.</li>
    <li>Sie bleiben allerdings bis zu einem Jahr im System, solange das Mitglied seinen Zugang nicht aktiviert hat.</li>
    <li>Abgelehnte Anträge werden wegen der Einspruchsmöglichkeit bei der Mitgliederversammlung 60 Wochen lang aufbewahrt</li>
</ul>

<table class="table">
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
