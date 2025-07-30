<div class="form-group row">
    <label for='input-<?=$name?>' class='col-sm-4 col-form-label'><?=$label?></label>
    <div class='col-sm-<?=$this->default($width, 8)?>'><?=$value->input(
        name: $name,
        required: $this->check($required),
        disabled: $this->check($disabled),
        type: $this->default($type, 'text'),
        placeholder: $label
    )?></div>
</div>
