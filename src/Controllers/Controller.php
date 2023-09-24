<?php

namespace Core\Controllers;

use Core\Exceptions\ParametersException;
use Core\Tools\Other;
use Rakit\Validation\Validator;

class Controller
{
    public static array $inputData;

    public function __construct()
    {
        foreach ($_GET as $key => $value) {
            $_GET[$key] = Other::correctType($value);
        }
        foreach ($_POST as $key => $value) {
            $_POST[$key] = Other::correctType($value);
        }
        self::$inputData = [
            "data" => $_SERVER["REQUEST_METHOD"] === "GET" ? $_GET : $_POST,
            "headers" => $_SERVER
        ];
    }

    /**
     * @param array $inputs
     * @param array $rules
     * @return void
     * @throws ParametersException
     */
    public function validateData(array $inputs, array $rules): void
    {
        $v = (new Validator())->validate($inputs, $rules);

        if (isset($v->errors->all()[0]))
            throw new ParametersException($v->errors->all()[0]);
    }
}