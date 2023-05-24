<?php

namespace Core\DTO;

class ErrorResponse
{
    private int $code = 500;
    private string $errorMessage;

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): ErrorResponse
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return ErrorResponse
     */
    public function setErrorMessage(string $errorMessage): ErrorResponse
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->code);
        die($this->toJson());
    }
}