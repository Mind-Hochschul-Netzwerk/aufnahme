    <div class="checkbox">
        <label for="input-{$name}">
            <input type="checkbox" id="input-{$name}" name="{$name}" value="j" {if $value && $value !== 'n'}checked="checked"{/if}/>
            {$label}
        </label>
    </div>
