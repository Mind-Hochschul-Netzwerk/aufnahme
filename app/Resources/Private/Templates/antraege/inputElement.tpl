<div class='col-sm-{$width|default:8}'><input id='input-{$name}' name='{$name}' type='{$type|default:text}' class='form-control' value="{$value|escape}" {if !empty($required)}required{/if} {if !empty($disabled)}disabled{/if} ></div>
