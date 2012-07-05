<?php
class CacheFS {
	
	public static function prepareFolder() {
		$path = Config::get('cache.path');
		if (file_exists($path) && is_dir($path)) {
			return TRUE;
		} else {
			return mkdir($path);
		}
	}
	
	public static function set($key, $value, $expires = NULL) {
		self::prepareFolder();
		$file = self::getFileName($key);
		if (is_null($expires))
			$expires = time() + Config::get('cache.timeout');
		else
			$expires = time() + $expires;
		
		$entry = array(
			'expires' => $expires,
			'created' => new DateTime(),
			'content' => serialize($value)
		);
		return file_put_contents($file, serialize($entry));
	}
	
	private static function getEntry($key) {
		self::prepareFolder();
		$now = time();
		$file = self::getFileName($key);
		$return = NULL;
		
		try {
			if (file_exists($file)) {
				$entry = file_get_contents($file);
				$entry = unserialize($entry);
		
				if ((int) $now > (int) $entry['expires']) {
					unlink($file);
				} else {
					$return = $entry;
				}
			}
		} catch (Exception $e) {
			$return = NULL;
		}
		
		return $return;
	}
	
	public static function info($key) {
		$entry = self::getEntry($key);
		if (!is_null($entry))
			unset($entry['content']);
		return $entry;
	}
	
	public static function get($key) {
		$entry = self::getEntry($key);
		
		if (!is_null($entry))
			$value = unserialize($entry['content']);
		else
			$value = NULL;
		
		return $value;
	}
	
	private static function getFileName($key) {
		return Config::get('cache.path') . $key;
	}
}