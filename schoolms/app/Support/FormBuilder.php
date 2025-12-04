<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Traits\Macroable;

class FormBuilder
{
    use Macroable;
    /**
     * Open a new HTML form.
     *
     * @param array $options
     * @return HtmlString
     */
    public function open(array $options = []): HtmlString
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $action = $options['url'] ?? ($options['route'] ?? null);

        if (isset($options['route'])) {
            $action = is_array($options['route'])
                ? route($options['route'][0], array_slice($options['route'], 1))
                : route($options['route']);
        }

        if (isset($options['url'])) {
            $action = is_array($options['url'])
                ? url($options['url'][0], array_slice($options['url'], 1))
                : url($options['url']);
        }

        $attributes = [];

        // Handle method spoofing for PUT, PATCH, DELETE
        $hiddenMethod = '';
        if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
            $hiddenMethod = '<input type="hidden" name="_method" value="' . $method . '">';
            $method = 'POST';
        }

        $attributes['method'] = $method;
        $attributes['action'] = $action;

        if (isset($options['files']) && $options['files']) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        if (isset($options['enctype'])) {
            $attributes['enctype'] = $options['enctype'];
        }

        if (isset($options['class'])) {
            $attributes['class'] = $options['class'];
        }

        if (isset($options['id'])) {
            $attributes['id'] = $options['id'];
        }

        if (isset($options['autocomplete'])) {
            $attributes['autocomplete'] = $options['autocomplete'];
        }

        $attributeString = $this->attributesToString($attributes);

        $csrf = '<input type="hidden" name="_token" value="' . csrf_token() . '">';

        return new HtmlString("<form{$attributeString}>{$csrf}{$hiddenMethod}");
    }

    /**
     * Close the form.
     *
     * @return HtmlString
     */
    public function close(): HtmlString
    {
        return new HtmlString('</form>');
    }

    /**
     * Create a text input.
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return HtmlString
     */
    public function text(string $name, $value = null, array $options = []): HtmlString
    {
        return $this->input('text', $name, $value, $options);
    }

    /**
     * Create a password input.
     *
     * @param string $name
     * @param array $options
     * @return HtmlString
     */
    public function password(string $name, array $options = []): HtmlString
    {
        return $this->input('password', $name, null, $options);
    }

    /**
     * Create a hidden input.
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return HtmlString
     */
    public function hidden(string $name, $value = null, array $options = []): HtmlString
    {
        return $this->input('hidden', $name, $value, $options);
    }

    /**
     * Create a number input.
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return HtmlString
     */
    public function number(string $name, $value = null, array $options = []): HtmlString
    {
        return $this->input('number', $name, $value, $options);
    }

    /**
     * Create an email input.
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return HtmlString
     */
    public function email(string $name, $value = null, array $options = []): HtmlString
    {
        return $this->input('email', $name, $value, $options);
    }

    /**
     * Create a file input.
     *
     * @param string $name
     * @param array $options
     * @return HtmlString
     */
    public function file(string $name, array $options = []): HtmlString
    {
        return $this->input('file', $name, null, $options);
    }

    /**
     * Create a textarea.
     *
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @return HtmlString
     */
    public function textarea(string $name, $value = null, array $options = []): HtmlString
    {
        $options['name'] = $name;
        $options['id'] = $options['id'] ?? $name;

        $attributeString = $this->attributesToString($options);
        $value = e($value);

        return new HtmlString("<textarea{$attributeString}>{$value}</textarea>");
    }

    /**
     * Create a select box.
     *
     * @param string $name
     * @param array $list
     * @param mixed $selected
     * @param array $options
     * @return HtmlString
     */
    public function select(string $name, array $list = [], $selected = null, array $options = []): HtmlString
    {
        $options['name'] = $name;
        $options['id'] = $options['id'] ?? $name;

        $attributeString = $this->attributesToString($options);

        $html = "<select{$attributeString}>";

        foreach ($list as $value => $display) {
            $isSelected = ($value == $selected) ? ' selected' : '';
            $html .= "<option value=\"" . e($value) . "\"{$isSelected}>" . e($display) . "</option>";
        }

        $html .= '</select>';

        return new HtmlString($html);
    }

    /**
     * Create a checkbox input.
     *
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return HtmlString
     */
    public function checkbox(string $name, $value = 1, $checked = false, array $options = []): HtmlString
    {
        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input('checkbox', $name, $value, $options);
    }

    /**
     * Create a radio input.
     *
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return HtmlString
     */
    public function radio(string $name, $value = null, $checked = false, array $options = []): HtmlString
    {
        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input('radio', $name, $value, $options);
    }

    /**
     * Create a submit button.
     *
     * @param string $value
     * @param array $options
     * @return HtmlString
     */
    public function submit($value = null, array $options = []): HtmlString
    {
        return $this->input('submit', null, $value, $options);
    }

    /**
     * Create a button element.
     *
     * @param string $value
     * @param array $options
     * @return HtmlString
     */
    public function button($value = null, array $options = []): HtmlString
    {
        $options['type'] = $options['type'] ?? 'button';
        $attributeString = $this->attributesToString($options);

        return new HtmlString("<button{$attributeString}>" . e($value) . "</button>");
    }

    /**
     * Create a label element.
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return HtmlString
     */
    public function label(string $name, $value = null, array $options = []): HtmlString
    {
        $options['for'] = $name;
        $attributeString = $this->attributesToString($options);

        return new HtmlString("<label{$attributeString}>" . e($value ?? $name) . "</label>");
    }

    /**
     * Create an input element.
     *
     * @param string $type
     * @param string|null $name
     * @param mixed $value
     * @param array $options
     * @return HtmlString
     */
    public function input(string $type, ?string $name = null, $value = null, array $options = []): HtmlString
    {
        $options['type'] = $type;

        if ($name) {
            $options['name'] = $name;
            $options['id'] = $options['id'] ?? $name;
        }

        if ($value !== null && $type !== 'password') {
            $options['value'] = $value;
        }

        $attributeString = $this->attributesToString($options);

        return new HtmlString("<input{$attributeString}>");
    }

    /**
     * Convert attributes array to string.
     *
     * @param array $attributes
     * @return string
     */
    protected function attributesToString(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " {$key}";
                }
            } elseif ($value !== null) {
                $html .= " {$key}=\"" . e($value) . "\"";
            }
        }

        return $html;
    }
}
