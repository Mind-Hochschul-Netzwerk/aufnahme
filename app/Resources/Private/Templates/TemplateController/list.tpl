<h2>Vorlagen und Texte bearbeiten</h2>

<ul>
{foreach from=$templates item=template}
    <li><a href="/templates/{$template->getName()|escape}">{$template->getLabel()|escape}</a></li>
{/foreach}
</ul>