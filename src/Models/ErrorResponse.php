<?php

namespace Core\Models;

class ErrorResponse
{
    public int $code;
    public string $errorMessage;

	/**
	 * @param string $errorMessage
	 * @param int $code
	 */
	public function __construct(string $errorMessage, int $code = 500)
	{
		$this->errorMessage = $errorMessage;
		$this->code = $code;
	}
}