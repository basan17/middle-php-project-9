<?php

namespace App;

use Valitron\Validator;

class UrlValidator
{
    public function validate(string $url): array
    {
        $errors = [];
        $v = new Validator(array('url' => $url));
        $v->rules([
            'lengthMax' => [
                ['url', 255]
            ],
            'required' => [
                ['url']
            ],
            'url' => [
                ['url']
            ]
        ]);
        // if (empty($url['name'])) {
        //     $errors['name'] = "Name can not be empty";
        // }

        // if (empty($url['model'])) {
        //     $errors['model'] = "Model can not be empty";
        // }
        if ($v->validate()) {
            return [];
        } else {
            return $v->errors()['url'];
        }
    }
}