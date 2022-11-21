<?php

namespace Core\DTO;

class Response
{
    private int $code = 200;
    private string $status;
    private array $response;

    /**
     * @param string $status
     * @return Response
     */
    public function setStatus(string $status): Response
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param array $response
     * @return Response
     */
    public function setResponse(array $response): Response
    {
        $this->response = $response;
        return $this;
    }

    public function setCode(int $code): Response {
        $this->code = $code;
        return $this;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        http_response_code($this->code);
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return void
     */
    public function send(): void
    {
        die(self::toJson());
    }
}