<?php
class CacheFS {
	
	public static function prepareFolder() {
		$path = sys_get_temp_dir();
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
			'content' => serialize($value)
		);
		return file_put_contents($file, serialize($entry));
	}
	
	public static function get($key) {
		self::prepareFolder();
		$now = time();
		$file = self::getFileName($key);
		$value = NULL;
		
		if (file_exists($file)) {
			$entry = file_get_contents($file);
			$entry = unserialize($entry);
			
			if ((int) $now > (int) $entry['expires']) {
				unlink($file);
			} else {
				$value = unserialize($entry['content']);
			}
		}
		
		return $value;
	}
	
	private static function getFileName($key) {
		return sys_get_temp_dir() ."/$key";
	}
}