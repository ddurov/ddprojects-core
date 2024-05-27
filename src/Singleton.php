<?php

namespace Core;

interface Singleton
{
	/**
	 * @return mixed
	 */
	public static function getInstance(): mixed;
}