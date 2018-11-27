<?php

	namespace Tochka;
	
class Cache
{
	private $expire;
	public function __construct($expire = 3600) {
		$this->expire = $expire;
		$files = glob('../cache/cache.*');
		if ($files) {
			foreach ($files as $file) {
				$filename = basename($file);
				$time = substr(strrchr($file, '.'), 1);
				if ($time < time()) {
					$this->delete(substr($filename, 6, strrpos($filename, '.') - 6));
				}
			}
		}
	}
	public function get($key) {
		$files = glob('../cache/cache.' . basename($key) . '.*');
		if ($files) {
			$handle = fopen($files[0], 'r');
			flock($handle, LOCK_SH);
			$size = filesize($files[0]);
			if ($size > 0) {
				$data = fread($handle, $size);
			} else {
				$data = '';
			}
			flock($handle, LOCK_UN);
			fclose($handle);
			return json_decode($data, true);
		}
		return false;
	}
	public function set($key, $value) {
		$this->delete($key);
		$file = '../cache/cache.' . basename($key) . '.' . (time() + $this->expire);
		$handle = fopen($file, 'w');
		flock($handle, LOCK_EX);
		fwrite($handle, json_encode($value));
		fflush($handle);
		flock($handle, LOCK_UN);
		fclose($handle);
	}
	public function delete($key) {
		$files = glob('../cache/cache.' . basename($key) . '.*');
		if ($files) {
			foreach ($files as $file) {
				if (!@unlink($file)) {
					clearstatcache(false, $file);
				}
			}
		}
	}
}