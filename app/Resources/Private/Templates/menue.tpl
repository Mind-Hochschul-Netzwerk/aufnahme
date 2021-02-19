{if $menue_vorhanden}
<ul>
{strip}
{section name=i loop=$Menue}
  {if $Menue[i].name =='--'}
     </ul>
     </div><div class="lighterBackground">
     <ul>
  {else}
     {if $Menue[i].name != '' && $Menue[i].nichtimmenue eq false} {*nur Menueeintrage mit Namen und die, die ins Menue sollen*}
     <li> 
       <a href="{$Menue[i].link}" title="{$Menue[i].title|escape}" {if $Menue[i].offen}class="offen"{/if}>
          {if $Menue[i].aktiv}<strong>{$Menue[i].name|escape}</strong>
          {else}{$Menue[i].name|escape}{/if}
       </a>
     </li>
     {/if}
   {/if}
{/section}
{/strip}
</ul>
{/if}
