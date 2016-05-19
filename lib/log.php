<?php

// Change the patch to your /vendor directory
// See the Elasticsearch PHP API document for more details
// http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/


namespace OCA\Eslog\Lib;

require_once 'apps/eslog/vendor/autoload.php';
use OCP\IUserSession;

class Log {

	/** IUserSession */
	private $userSession;

	/**
	* @param User $ocuser
	**/
	public function __construct(
		IUserSession $userSession
	){		
		$this->userSession = $userSession;
	}

	public static function log($path,$path2,$action){

		$protocol = 'http';
		if(!empty($_SERVER['PHP_AUTH_USER'])) {
			$user = $this->userSession->getUser()->getUID();
		}

		$folder = is_array($path)?dirname($path['path']):dirname($path);
		$file = is_array($path)?basename($path['path']):basename($path);
		
		if (empty($folder2))
			$folder2 = is_array($path2)?dirname($path2['path']):(!empty($path2)?dirname($path2):'');
		$file2 = is_array($path2)?basename($path2['path']):(!empty($path2)?basename($path2):'');		
		$type='unknown';
		
		if(!empty($file2)){
			$type = \OC\Files\Filesystem::filetype($folder2.'/'.$file2); 
		
			if(strpos($type,';')){
				$type=substr($type,0,strpos($type,';'));
			}
		} 
		
		self::send2elasticsearch($user, $protocol, $type, $folder, $file,$folder2,$file2, $action);
	}
	
	public static function send2elasticsearch($user, $protocol, $type, $folder, $file,$folder2,$file2, $action)
	{
		$params = array();
		$params['hosts'] = array(
			\OCP\Config::getAppValue('eslog', 'eslog_host', 'localhost:9200')
		);
		if (\OCP\Config::getAppValue('eslog', 'eslog_auth', 'none') != "none") {
			$params['connectionParams']['auth'] = array(
				\OCP\Config::getAppValue('eslog', 'eslog_user', ''),
				\OCP\Config::getAppValue('eslog', 'eslog_password', ''),
				\OCP\Config::getAppValue('eslog', 'eslog_auth', 'none')
			);
		}

		$client = \Elasticsearch\ClientBuilder::create()->setHosts(array('10.2.20.36:9200'))->build();
		$date = date('c');
		$request=$_REQUEST;
		if(isset($request['password'])) $request['password']='******';
		
		$server=$_SERVER;
		if(isset($server['PHP_AUTH_PW'])) $server['PHP_AUTH_PW']='******';
		if(isset($server['HTTP_COOKIE'])) $server['HTTP_COOKIE']='******';
		if(isset($server['HTTP_AUTHORIZATION'])) $server['HTTP_AUTHORIZATION']='******';

		if (isset($server['REMOTE_ADDR'])) {
			$remote_address = $server['REMOTE_ADDR'];
		}
		if (isset($server['REMOTE_PORT'])) {
			$remote_port = $server['REMOTE_PORT'];
		}
		
		$vars=serialize(array(
			'request'=>$request,
			'server'=>$server
		));
		$params = array();
		$params['index'] = \OCP\Config::getAppValue('eslog', 'eslog_index', 'owncloud');
		$params['type'] = \OCP\Config::getAppValue('eslog', 'eslog_type', 'owncloud');
		$params['body'] = array(
			'@timestamp' => $date,
			'user' => $user,
			'src_ip' => $remote_address,
			'src_port' => $remote_port,
			'date' => $date,
			'proto' => $protocol,
			'content_type' => $type,
			'folder' => $folder,
			'file' => $file,
			'folder2' => $folder2,
			'file2' => $file2,
			'action' => $action,
			'variables' => $vars
		);
		$ret = $client->index($params);
	}
}
