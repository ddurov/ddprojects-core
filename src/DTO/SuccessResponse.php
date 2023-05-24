<?php

namespace Core\DTO;

class SuccessResponse extends Response
{
    private string $body;

    /**
     * @return string
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
}