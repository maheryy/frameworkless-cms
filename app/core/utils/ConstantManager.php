<?php

namespace App\Core\Utils;

abstract class ConstantManager
{

	public static $env_path = './config/.env';

	public static function defineConstant(string $key, string $value)
	{
		$key = str_replace(' ', '_', mb_strtoupper(trim($key)));
		if (!defined($key)) {
			define($key, trim($value));
		} else {
			throw new \Exception('la constante ' . $key . ' existe déjà');
		}
	}

	public static function loadConstants()
	{
		if (!file_exists(self::$env_path)) {
			throw new \Exception('Le fichier ' . self::$env_path . ' n\'existe pas');
		}
		# Constants in config/conf.inc.php
		include './config/conf.inc.php';

		# Constants in .env
		$env = fopen(self::$env_path, 'r');
		if (!empty($env)) {
			while (!feof($env)) {
				$line = trim(fgets($env));
				$preg_results = [];
				if (preg_match('/([^=]*)=([^#]*)/', $line, $preg_results) && !empty($preg_results[1]) && !empty($preg_results[2])) {
					self::defineConstant($preg_results[1], $preg_results[2]);
				}
			}
		}
	}
}
