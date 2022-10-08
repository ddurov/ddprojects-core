<?php

namespace Core;

use Core\Contracts\Singleton;
use PHPMailer\PHPMailer\PHPMailer;

class Mail implements Singleton
{
    private static ?PHPMailer $instance = null;

    public static function getInstance(): PHPMailer
    {
        if (self::$instance === null) {
            self::$instance = new PHPMailer(true);
            self::$instance->isSMTP();
            self::$instance->Host = getenv("MAIL_SERVER");
            self::$instance->SMTPAuth = true;
            self::$instance->Username = getenv("MAIL_USER");
            self::$instance->Password = getenv("MAIL_PASSWORD");
            self::$instance->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            self::$instance->Port = 587;
            self::$instance->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            self::$instance->XMailer = 'meow-ler';
            self::$instance->CharSet = 'utf-8';
        }

        return self::$instance;
    }

    //singleton
    private function __construct(){}

    private function __clone(){}
}