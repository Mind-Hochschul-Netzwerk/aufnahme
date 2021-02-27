<h1>Offene Anträge</h1>

{if !empty($nichtvonmirgevotet)}
    <p><em>Hinweis:</em> Es werden im Augenblick nur offene Anträge gezeigt, bei denen du entweder noch kein Votum abgegeben hast,
    oder die den Status "neu bewerten" haben und dein Votum älter ist als diese Statusänderung.</p>
    <p><a href="/antraege/">Alle offene Anträge zeigen</a></p>
{else}
    <p><a href="/antraege/nichtvonmirgevotet/">Nur Anträge anzeigen, für die ich noch abstimmen muss</a></p>
{/if}

<p>Farben: rot für Anträge, die älter sind als 4 Wochen, gelb für Anträge älter als 2.<br />
Der Status wird grün eingefärbt, falls es 3 oder mehr "Ja"- bei keinen "Nein"- und "Nachfragen"-Voten gibt.<br/>
Beim Status "Auf Antwort warten" ist die Farbe grau, egal wie alt er ist.</p>

<div class="table-responsive"><table class="table">
    {foreach from=$antraege key=index item=a}
        {if $index % 20 === 0}
        <tr><th>Nr.</th><th>Antragssteller</th><th>Antragsdatum</th><th>Status</th><th>Statusdatum</th>
            {foreach from=$userNames_gevotet item=i}
                <th>{$realNames.$i|escape}</th>
            {/foreach}
            <th>Bemerkung</th>
        </tr>
        {/if}
        <tr>
            <td><a href="/antraege/{$a->getID()}/">{$a->getID()}</a></td>
            <td class="UA{$a->getDringlichkeit()}">{$a->getName()|escape}</td>
            <td class="UA{$a->getDringlichkeit()}">{$a->getDatumAntrag()}</td>
            <td{if $a->getGruen()} class="gruen"{/if}>{$a->getStatusReadable()}</td>
            <td>{$a->getDatumStatusaenderung()}</td>
            {foreach from=$userNames_gevotet item=userName}
                <td class="{$a->getLatestVoteColorByUserName($userName)}">{$a->getLatestVoteReadableByUserName($userName)}</td>
            {/foreach}
            <td>{$a->getBemerkung()|escape}</td>
        </tr>
    {/foreach}
</table></div>
