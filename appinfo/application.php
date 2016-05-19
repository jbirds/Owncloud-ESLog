<?php

namespace OCA\Eslog\Appinfo;

use \OCP\AppFramework\App;
use \OCP\API;
use \OCP\AppFramework\IAppContainer;
use \OCA\Eslog\Lib\Hooks;

class Application extends App {
	public function __construct(array $urlParams=array()) {
		parent::__construct('eslog', $urlParams);
		$container = $this->getContainer();
		$server = $container->getServer();

		$container->registerService('OC_esLog', function(IAppContainer $c) use ($server) {
			return new OC_esLog(
				$server->getUserSession()
			);
		});
	}
	
	public function registerSettings() {
		\OCP\App::registerAdmin('eslog','settings');
		\OCP\App::registerPersonal('eslog','settings');
	}	

	public function registerEslogConfig() {
		$conf = array(
			'eslog_auth' => 'none',
			'eslog_user' => '',
			'eslog_password' => '',
			'eslog_index' => 'owncloud',
			'eslog_type' => 'owncloud',
		);

		foreach ($conf as $key => $value) {
			\OCP\Config::setAppValue('eslog', $key, $value);
		}	
	}
}	
