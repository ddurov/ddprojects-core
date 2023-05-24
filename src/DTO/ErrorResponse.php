<?php

namespace Core\DTO;

class ErrorResponse extends Response
{
    private string $errorMessage;

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
}