<?php

namespace Hestia\WebApp\Installers\NodeJs\NodeJsUtils;

use Hestia\System\HestiaApp;

class NodeJsUtil {

	protected $appcontext;

	public function __construct(HestiaApp $appcontext) {
		$this->appcontext = $appcontext;
	}

	public function createDir(string $dir) {
		$result = null;
		
		if (!is_dir($dir)) {
			$this->appcontext->runUser('v-add-fs-directory', [$dir], $result);
		}

		return $result;
	}
	public function moveFile(string $fileA, string $fileB) {
		$result = null;
		
		if (!$this->appcontext->runUser('v-move-fs-file', [$fileA, $fileB], $result)) {
			throw new \Exception("Error updating file in: " . $fileA . " " . $result->text);
		}

		return $result;
	}
	public function parseTemplate($template, $search, $replace): array {
		$data = [];
		
		$file = fopen($template, 'r');
		while ($l = fgets($file)) {
			$data[] = str_replace($search, $replace, $l);
		}
		fclose($file);

		return $data;
	}
}