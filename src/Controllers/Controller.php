<?php

namespace Core\Controllers;

class Controller
{
    public static array $inputData;

    public function __construct()
    {
        self::$inputData = [
            "data" => $_SERVER["REQUEST_METHOD"] === "GET" ? $_GET : $_POST,
            "headers" => $_SERVER
        ];
    }
}