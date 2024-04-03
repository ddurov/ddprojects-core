<?php declare(strict_types=1);

namespace Core\Tools;

class Other
{

    /**
     * @param string $folder
     * @param string $logName
     * @param mixed $value
     * @return void
     */
    public static function log(string $folder, string $logName, mixed $value): void
    {
        $value = var_export($value, true);
        $time = date('D M j G:i:s');
        file_put_contents("$folder/$logName.log", "[$time]: $value\n", FILE_APPEND);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function correctType(mixed $value): mixed
    {
        return json_decode($value) ?? $value;
    }
}