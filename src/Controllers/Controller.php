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
            $_GET[$key] = $this->correctValue($value);
        }
        foreach ($_POST as $key => $value) {
            $_POST[$key] = $this->correctValue($value);
        }
        self::$inputData = [
            "data" => $_SERVER["REQUEST_METHOD"] === "GET" ? $_GET : $_POST,
            "headers" => $_SERVER
        ];
    }

    private function correctValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $corrected = [];
            foreach ($value as $keyItem => $valueItem) {
                $corrected[$keyItem] = (is_array($valueItem)) ? $this->correctValue($valueItem) : Other::correctType($valueItem);
            }
            return $corrected;
        } else return Other::correctType($value);
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