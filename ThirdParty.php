<?php
namespace core;
define('VENDOR',EURAXLUO . "/vendor/autoload.php");
class ThirdParty
{
	static function init(){
		if (is_file(VENDOR)) {
			include "vendor/autoload.php";
			return true;
		}
		return false;
	}
	static function initWhoops()
	{
		if (INCLUDE_COMPOSER) {//如果开启debug模式，才引用whoops
			$whoops = new \Whoops\Run;
			$errorTitle = 'verb framework error';
			$option = new \Whoops\Handler\PrettyPageHandler();
			$option->setPageTitle($errorTitle);
			$whoops->pushHandler($option);
			$whoops->register();
		}

	}
}
