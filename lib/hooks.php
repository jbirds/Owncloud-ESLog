<?php

namespace OCA\Eslog\Lib;
use OCA\Eslog\Lib\Log;
use OC\Files\View;

class Hooks {

	public function __construct() {
		
	}

	public static function connectHooks() {
		\OCP\Util::connectHook('OC_User', 'pre_login', 'OCA\Eslog\Lib\Hooks', 'prelogin');
		\OCP\Util::connectHook('OC_User', 'post_login', 'OCA\Eslog\Lib\Hooks', 'login');
		\OCP\Util::connectHook('OC_User', 'logout', 'OCA\Eslog\Lib\Hooks', 'logout');
		\OCP\Util::connectHook('OC_Filesystem', 'touch', 'OCA\Eslog\Lib\Hooks', 'login');
		\OCP\Util::connectHook('\OCP\Files', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OCP\Files', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('\OCP\Files\Node', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OCP\Files\Node', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OC_Filesystem', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('\OCP\Files\View', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OCP\Files\View', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OCP\Files', 'post_touch', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OC_Filesystem', 'read', 'OCA\Eslog\Lib\Hooks', 'read');
		\OCP\Util::connectHook('OC_Filesystem', 'post_write', 'OCA\Eslog\Lib\Hooks', 'write');
		\OCP\Util::connectHook('OC_Filesystem', 'delete', 'OCA\Eslog\Lib\Hooks', 'deleteFile');
		\OCP\Util::connectHook('OC\Files', 'preTouch', 'OCA\Eslog\Lib\Hooks', 'touch');
		\OCP\Util::connectHook('OC_Filesystem', 'preTouch', 'OCA\Eslog\Lib\Hooks', 'touch');
		\OCP\Util::connectHook('OC_Filesystem', 'post_rename', 'OCA\Eslog\Lib\Hooks', 'rename');
		\OCP\Util::connectHook('OC_Filesystem', 'post_delete', 'OCA\Eslog\Lib\Hooks', 'deleteFile');
		\OCP\Util::connectHook('OC_Filesystem', 'copy', 'OCA\Eslog\Lib\Hooks', 'copy');
	}	

	// ----------------
	// Users management
	// ----------------

	public static function prelogin($vars) {
		Helper::registerHooks();
		Log::log($vars['uid'],'/','User login attempt');
	}
	
	public static function login($vars) {
		Helper::registerHooks();
		Log::log($vars['uid'],'/','User login');
	}	

	public static function logout($vars) {
		Helper::registerHooks();
		Log::log($vars['uid'],'/','User logout');
	}

	// ---------------------
	// Filesystem operations
	// ---------------------

	public static function read($path) {	
		Helper::read();
		Log::log($path,NULL,'File read');
	}
	
	public static function touchFile($path) {
		error_log("I AM NOT TOUCHING YOU", 0);
	}

	public static function write($path) {
		Helper::write();
		Log::log($path,NULL,'File written');
	}

	public static function deleteFile($path) {
		Helper::delete();
		Log::log($path,NULL,'File deleted');
	}

	public static function rename($paths) {
		if(isset($_REQUEST['target'])) {
			Helper::registerHooks();
			Log::log($paths['oldpath'],$paths['newpath'],'File moved');		}
		else {
			Helper::registerHooks();
			Log::log($paths['oldpath'],$paths['newpath'],'File renamed');
		}	

	}
	
	public static function copy($paths) {
		Helper::registerHooks();
		Log::log($paths['oldpath'],$paths['newpath'],'File copied');
	}

	public static function defaulthook($vars) {
		$action='unknown';
		$path=$vars;
		$protocol='http';

		if(isset($vars['SCRIPT_NAME']) && basename($vars['SCRIPT_NAME']) == 'remote.php') {
			$paths=explode('/',$vars['REQUEST_URI']);
			$pos=array_search('remote.php',  $paths);
			$protocol=$paths[$pos+1];
			$path='';
			for($i=$pos+2 ; $i<sizeof($paths) ; $i++) {
				$path.='/'.$paths[$i];
			}
			
			$action=strlower($vars['REQUEST_METHOD']);
		
			if($protocol=='webdav') {
				if($action=='put') $action='write';
			}
		}

		if(!in_array($action,array('head', 'propfind'))) {
			Log::log($path,NULL,$action,$protocol);
		}
	}

}
