<?php

namespace Core;

use Core\Exceptions\ParametersException;
use Core\Models\ErrorResponse;
use Core\Models\SuccessResponse;
use JetBrains\PhpStorm\NoReturn;
use Rakit\Validation\Validator;

class Controller
{
	public array $data;
	public array $headers;

	public function __construct()
	{
		foreach ($_GET as $key => $value) {
			$_GET[$key] = Tools::correctValue($value);
		}
		foreach ($_POST as $key => $value) {
			$_POST[$key] = Tools::correctValue($value);
		}
		$this->data = $_SERVER["REQUEST_METHOD"] === "GET" ? $_GET : $_POST;
		$this->headers = $_SERVER;
	}

	#[NoReturn]
	public static function sendResponse(SuccessResponse|ErrorResponse $response): void
	{
		http_response_code($response->code);
		die(json_encode(get_object_vars($response), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
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