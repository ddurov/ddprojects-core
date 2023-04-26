<?php declare(strict_types=1);

namespace Core\Tools;

class Other
{
    /**
     * @param mixed $value
     * @param string $product
     */
    public static function log(mixed $value, string $product = "general"): void
    {
        $value = var_export($value, true);
        $time = date('D M j G:i:s');
        file_put_contents("/tmp/ddLogs/$product.log", "[$time]: $value\n", FILE_APPEND);
    }
}