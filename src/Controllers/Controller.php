<?php

namespace Core\Controllers;

use Core\DTO\ErrorResponse;
use Core\DTO\SuccessResponse;
use Core\Exceptions\ParametersException;
use Core\Tools\Other;
use JetBrains\PhpStorm\NoReturn;
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

	#[NoReturn]
	public function sendResponse(SuccessResponse|ErrorResponse $response): void
	{
		http_response_code($response->code);
		die(json_encode(get_object_vars($response), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
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