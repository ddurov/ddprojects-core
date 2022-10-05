<?php declare(strict_types=1);

namespace Core\Controllers;

class Controller
{
    public array $inputData;

    public function __construct()
    {
        $this->inputData = [
            "data" => $_SERVER["REQUEST_METHOD"] === "GET" ? $_GET : $_POST,
            "headers" => $_SERVER
        ];
    }
}