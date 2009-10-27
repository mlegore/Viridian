<?php

final class App {
	
	private $rules = array();
	private $name;
	private $uri;
	
	private static $instance;
	
	public function __construct($host,$path) {
		self::$instance = $this;
		include 'apps.php';
		
		//Find the current app based on rules/etc.
		foreach($this -> rules as $rule) {
			//printr($rule);
			if ($arr = match($rule['rule'],$host,$path)) {
				if ( is_numeric($rule['app']) ) {
					$app = $arr['matches'][$rule['app']];
				} else {
					$app = $rule['app'];
				}
				$this -> remainingUriPath = $arr['path'];
				break;
			}
		}
		if ( !isset($app) ) {
			$app = 'app';
			$this -> remainingUriPath = $path;
			
		}
		define('CURRENT_APP',$app);
		define('PRIMARY_APP',$app);
	}
	
	private function bind($rule,$app,$type=WILDCARD) {
		if(!isset($rule['type'])) {
			$rule['type'] = $type;
		}
		$this -> rules[] = array('rule'=>$rule,'app'=>$app);
	}
	
	public function uriPath() {
		return $this -> remainingUriPath;
	}
	
	public static function & instance() {
		return self::$instance;
	}
	
}

?>