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
		    var_export($value, true)
	    );
    }

	public static function correctValue(mixed $value): mixed
	{
		if (is_array($value)) {
			$corrected = [];
			foreach ($value as $keyItem => $valueItem) {
				$corrected[$keyItem] = (is_array($valueItem)) ?
					self::correctValue($valueItem) : (json_decode($valueItem) ?? $valueItem);
			}
			return $corrected;
		} else return (json_decode($value) ?? $value);
	}
}