<?php
declare(strict_types=1);
namespace App\Service;

class TemplateVariable extends \Hengeb\Simplates\TemplateVariable {
    private function defaultAttributes(array $attributes): array
    {
        $attributes['name'] ??= preg_match('/\[(.+)\]/', $this->name, $m) ? $m[1] : $this->name;
        $attributes['id'] ??= 'input-' . $attributes['name'];
        $attributes['class'] ??= 'form-control';

        return $attributes;
    }

    public function input(...$attributes): string
    {
        $attributes = $this->defaultAttributes($attributes);
        $attributes['maxlength'] ??= 127;
        return parent::input(...$attributes);
    }

    public function box(...$attributes): string
    {
        $attributes = $this->defaultAttributes($attributes);
        $attributes['class'] = '';
        return parent::box(...$attributes);
    }

    public function textarea(...$attributes): string
    {
        return parent::textarea(...$this->defaultAttributes($attributes));
    }

    public function select(array $options, ...$attributes): string
    {
        return parent::select($options, ...$this->defaultAttributes($attributes));
    }
}
