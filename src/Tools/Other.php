<?php declare(strict_types=1);

namespace Core\Tools;

class Other
{
    /**
     * @param mixed $value
     */
    public static function log(mixed $value): void
    {
        $value = var_export($value, true);
        $time = date('D M j G:i:s');
        file_put_contents("/tmp/ddCoreLogs.txt", "[$time]: $value\n", FILE_APPEND);
    }
}