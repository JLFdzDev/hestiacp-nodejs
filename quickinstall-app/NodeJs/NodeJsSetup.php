<?php

namespace Hestia\WebApp\Installers\NodeJs;

use \Hestia\WebApp\Installers\BaseSetup as BaseSetup;
use \Hestia\WebApp\Installers\NodeJs\NodeJsUtils\NodeJsPaths as NodeJsPaths;
use \Hestia\WebApp\Installers\NodeJs\NodeJsUtils\NodeJsUtil as NodeJsUtil;
use Hestia\System\HestiaApp;

class NodeJsSetup extends BaseSetup {

	protected const TEMPLATE_PROXY_VARS = ['%nginx_port%'];
	protected const TEMPLATE_ENTRYPOINT_VARS = ['%app_name%', '%app_start_script%', '%app_cwd%'];

	protected $nodeJsPaths;
	protected $nodeJsUtils;
    protected $appInfo = [ 
        'name' => 'NodeJs',
        'group' => 'node',
        'enabled' => true,
        'version' => '1.0.0',
        'thumbnail' => 'nodejs.png'
    ];
    protected $appname = 'NodeJs';
    protected $config = [
        'form' => [
            'node_version' => [ 
                'type' => 'select',
                'options' => ['v20.10.0', 'v18.18.2', 'v16.20.2'],
            ],
            'start_script' => ['type'=>'text', 'placeholder'=>'npm run start'],
            'port' => ['type' => 'text', 'placeholder' => '3000'],
        ],
        'database' => false,
        'server' => [
            'php' => [
                'supported' => [ '7.2','7.3','7.4','8.0','8.1','8.2' ],
            ]
        ],
    ];

	public function __construct($domain, HestiaApp $appcontext) {
		parent::__construct($domain, $appcontext);

		$this->nodeJsPaths = new NodeJsPaths($appcontext);
		$this->nodeJsUtils = new NodeJsUtil($appcontext);
	}
	
    public function install(array $options = null) {
		$this->createAppDir();
		$this->createConfDir();
		$this->createAppEntryPoint($options);
		$this->createAppNvmVersion($options);
		$this->createAppEnv($options);
		$this->createPublicHtmlConfigFile();
		$this->createAppProxyTemplates($options);
		$this->createAppConfig($options);
		$this->pm2StartApp();

		return true;
    }

	public function createAppEntryPoint(array $options = null) {
		$templateReplaceVars = [$this->domain, trim($options['start_script']), $this->nodeJsPaths->getAppDir($this->domain)];
		
		$data = $this->nodeJsUtils->parseTemplate($this->nodeJsPaths->getAppEntrypointTemplate(), self::TEMPLATE_ENTRYPOINT_VARS, $templateReplaceVars);
		$tmpFile = $this->saveTempFile(implode($data));

		return $this->nodeJsUtils->moveFile(
			$tmpFile, 
			$this->nodeJsPaths->getAppEntryPoint($this->domain)
		);
	}

	public function createAppNvmVersion($options) {
		$tmpFile = $this->saveTempFile($options['node_version']);

		return $this->nodeJsUtils->moveFile(
			$tmpFile, 
			$this->nodeJsPaths->getAppDir($this->domain, '.nvmrc')
		);
	}

	public function createAppEnv($options) {
		$data = 'PORT="'. trim($options['port']) . '"';
		
		$tmpFile = $this->saveTempFile($data);

		return $this->nodeJsUtils->moveFile(
			$tmpFile, 
			$this->nodeJsPaths->getAppDir($this->domain, '.env')
		);
	}

	public function createAppProxyTemplates(array $options = null) {
		$tplReplace = [trim($options['port'])];

		$proxyData = $this->nodeJsUtils->parseTemplate(
			$this->nodeJsPaths->getNodeJsProxyTemplate(), 
			self::TEMPLATE_PROXY_VARS, 
			$tplReplace
		);
		$proxyFallbackData = $this->nodeJsUtils->parseTemplate(
			$this->nodeJsPaths->getNodeJsProxyFallbackTemplate(), 
			self::TEMPLATE_PROXY_VARS,
			$tplReplace
		);

		$tmpProxyFile = $this->saveTempFile(implode($proxyData));
		$tmpProxyFallbackFile = $this->saveTempFile(implode($proxyFallbackData));

		$this->nodeJsUtils->moveFile(
			$tmpProxyFile, 
			$this->nodeJsPaths->getAppProxyConfig($this->domain)
		);
		$this->nodeJsUtils->moveFile(
			$tmpProxyFallbackFile, 
			$this->nodeJsPaths->getAppProxyFallbackConfig($this->domain)
		);
	}

	public function createAppConfig(array $options = null) {
		$config = 'PORT=' . trim($options['port']) . '|START_SCRIPT="' . trim($options['start_script']) . '"|NODE_VERSION=' . trim($options['node_version']);
		$file = $this->saveTempFile($config);

		return $this->nodeJsUtils->moveFile(
			$file, 
			$this->nodeJsPaths->getConfigFile($this->domain)
		);
	}

	public function createPublicHtmlConfigFile() {
		// This file is created for hestia to detect that there is an installed app when you try to install other app
		$this->appcontext->runUser('v-add-fs-file', [$this->getDocRoot('app.conf')]);
	}

	public function createAppDir() {
		$this->nodeJsUtils->createDir($this->nodeJsPaths->getAppDir($this->domain));
	}

	public function createConfDir() {
		$this->nodeJsUtils->createDir($this->nodeJsPaths->getConfigDir());
		$this->nodeJsUtils->createDir($this->nodeJsPaths->getConfigDir('/web'));
		$this->nodeJsUtils->createDir($this->nodeJsPaths->getDomainConfigDir($this->domain));
	}

	public function pm2StartApp() {
		return $this->appcontext->runUser('v-add-pm2-app', [$this->domain, $this->nodeJsPaths->getAppEntryPointFileName()]);
	}
}