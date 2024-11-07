<?php

namespace Core\DTO;

class SuccessResponse
{
    public int $code;
    public mixed $body;

	/**
	 * @param mixed $body
	 * @param int $code
	 */
	public function __construct(mixed $body, int $code = 200)
	{
		$this->body = $body;
		$this->code = $code;
	}
}