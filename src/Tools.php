<?php declare(strict_types=1);

namespace Core;

class Tools
{
	/**
	 * @param int $type
	 * @param mixed $value
	 * @return void
	 */
	public static function log(int $type, mixed $value): void
    {
	    fwrite(
		    ($type === 0) ? fopen('php://stdout', 'w') : fopen('php://stderr', 'w'),
		    $value
	    );
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