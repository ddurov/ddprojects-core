<?php

namespace Core\DTO;

class SuccessResponse
{
    private int $code = 200;
    private mixed $body;

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return SuccessResponse
     */
    public function setCode(int $code): SuccessResponse
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return SuccessResponse
     */
    public function setBody(mixed $body): SuccessResponse
    {
        $this->body = $body;
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