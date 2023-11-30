<?php

namespace Hestia\WebApp\Installers\NodeJs\NodeJsUtils;

use Hestia\System\HestiaApp;
use Hestia\System\Util;

class NodeJsPaths {
	private const APP_DIR = 'private/nodeapp';
	private const CONFIG_DIR = 'hestiacp_nodejs_config';
	private const APP_CONFIG_FILE_NAME = '.conf';
	private const APP_ENTRYPOINT_NAME = 'ecosystem.config.js';
	private const APP_ENTRYPOINT_TEMPLATE = __DIR__ . '/../templates/web/entrypoint.tpl';
	private const APP_PROXY_CONFIG_FILE_NAME = 'nodejs-app.conf';
	private const APP_PROXY_FALLBACK_CONFIG_FILE_NAME = 'nodejs-app-fallback.conf';
	private const NODEJS_PROXY_CONFIG_TEMPLATE = __DIR__ . '/../templates/nginx/nodejs-app.tpl';
	private const NODEJS_PROXY_FALLBACK_CONFIG_TEMPLATE = __DIR__ . '/../templates/nginx/nodejs-app-fallback.tpl';
	
	protected $appcontext;

	public function __construct(HestiaApp $appcontext) {
		$this->appcontext = $appcontext;
	}
	public function getAppDir(string $domain, string $relativePath = null): string {
		$domainPath = $this->appcontext->getWebDomainPath($domain);

		if (empty($domainPath) || !is_dir($domainPath)) {
			throw new \Exception("Error finding domain folder ($domainPath)");
		}
		
		return Util::join_paths($domainPath, self::APP_DIR, $relativePath);
	}

	public function getConfigDir(string $relativePath = null): string {
		$userHome = $this->appcontext->getUserHomeDir();

		if (empty($userHome) || !is_dir($userHome)) {
			throw new \Exception("Error finding user home ($userHome)");
		}
		
		return Util::join_paths($userHome, self::CONFIG_DIR, $relativePath);
	}

	public function getDomainConfigDir(string $domain, string $relativePath = null): string {
		return Util::join_paths($this->getConfigDir('/web/' . $domain), $relativePath);
	}

	public function getConfigFile(string $domain): string {
		return $this->getDomainConfigDir($domain, self::APP_CONFIG_FILE_NAME);
	}

	public function getAppEntryPoint(string $domain): string {
		return $this->getAppDir($domain, self::APP_ENTRYPOINT_NAME);
	}

	public function getAppEntryPointFileName(): string {
		return self::APP_ENTRYPOINT_NAME;
	}

	public function getAppProxyConfig(string $domain): string {
		return $this->getDomainConfigDir($domain, self::APP_PROXY_CONFIG_FILE_NAME);
	}

	public function getAppProxyFallbackConfig(string $domain): string {
		return $this->getDomainConfigDir($domain, self::APP_PROXY_FALLBACK_CONFIG_FILE_NAME);
	}

	public function getNodeJsProxyTemplate() {
		return self::NODEJS_PROXY_CONFIG_TEMPLATE;
	}

	public function getNodeJsProxyFallbackTemplate() {
		return self::NODEJS_PROXY_FALLBACK_CONFIG_TEMPLATE;
	}

	public function getAppEntrypointTemplate() {
		return self::APP_ENTRYPOINT_TEMPLATE;
	}
}