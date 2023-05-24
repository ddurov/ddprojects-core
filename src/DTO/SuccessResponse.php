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
     * @param string $body
     * @return SuccessResponse
     */
    public function setBody(string $body): SuccessResponse
    {
        $this->body = $body;
        return $this;
    }
}