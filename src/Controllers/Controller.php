<?php

namespace Core\Controllers;

use Core\Exceptions\InvalidParameter;
use Rakit\Validation\Validator;

class Controller
{
    public static array $inputData;

    public function __construct()
    {
        self::$inputData = [
            "data" => $_SERVER["REQUEST_METHOD"] === "GET" ? $_GET : $_POST,
            "headers" => $_SERVER
        ];
    }

    /**
     * @param array $inputs
     * @param array $rules
     * @return void
     * @throws InvalidParameter
     */
    public function validateData(array $inputs, array $rules): void
    {
        $v = (new Validator())->validate($inputs, $rules);

        if (isset($v->errors->all()[0]))
            throw new InvalidParameter($v->errors->all()[0]);
    }
}