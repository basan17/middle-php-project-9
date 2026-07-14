<?php

namespace App;

use Valitron\Validator;

class UrlValidator
{
    public function validate(string $url): array
    {
        $v = new Validator(array('url' => $url));

        $v->rule('required', 'url')->message('{field} не должен быть пустым')->label('URL');
        $v->rule('lengthMax', 'url', 255)->message('{field} превышает 255 символов')->label('URL');
        $v->rule('url', 'url')->message('Некорректный {field}')->label('URL');

        if ($v->validate()) {
            return [];
        } else {
            return $v->errors()['url'];
        }
    }
}