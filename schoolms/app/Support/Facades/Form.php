<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString open(array $options = [])
 * @method static \Illuminate\Support\HtmlString close()
 * @method static \Illuminate\Support\HtmlString text(string $name, $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString password(string $name, array $options = [])
 * @method static \Illuminate\Support\HtmlString hidden(string $name, $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString number(string $name, $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString email(string $name, $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString file(string $name, array $options = [])
 * @method static \Illuminate\Support\HtmlString textarea(string $name, $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString select(string $name, array $list = [], $selected = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString checkbox(string $name, $value = 1, $checked = false, array $options = [])
 * @method static \Illuminate\Support\HtmlString radio(string $name, $value = null, $checked = false, array $options = [])
 * @method static \Illuminate\Support\HtmlString submit($value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString button($value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString label(string $name, $value = null, array $options = [])
 * @method static \Illuminate\Support\HtmlString input(string $type, ?string $name = null, $value = null, array $options = [])
 *
 * @see \App\Support\FormBuilder
 */
class Form extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'form';
    }
}
