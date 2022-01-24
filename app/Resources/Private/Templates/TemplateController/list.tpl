<h2>Vorlagen bearbeiten</h2>

<ul>
{foreach from=$templates item=template}
    <li><a href="{$template->getName()|escape}">{$template->getLabel()|escape}</a></li>
{/foreach}
</ul>