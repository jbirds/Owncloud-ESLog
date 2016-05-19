<?php

namespace OCA\Eslog\Lib;

use OC\Files\Filesystem;
use OC\Files\View;
use OCP\User;

class Helper {

	public static function registerHooks() {
		error_log("I FUCKING GOT IN.", 0);
	}

	public static function read() {
		error_log("FUCKING READ BITCH.", 0);
	}

	public static function write() {
		error_log("I WROTE BITCH.", 0);
	}

	public static function delete() {
		error_log("I DELETED BITCH", 0);
	}
}		
