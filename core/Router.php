<?php

/*
 * Router Class
 *
 * This class handles all of the controller routing based on the uri
 * Has the ability to route passthru directories, forward to a specified url, or route to the controller
 * 
 * @package		Viridian
 * @author		Michael LeGore
 * @version		1.0
 * 
 */

class Router extends Base {
	
	public $app;
	public $controller;
	public $action;
	protected $rules = array(); 
	
	const ruleRegex = true;
	const ruleWildcard = false;
	
	const normal = 0;
	const passThru = 1;
	const redirect = 2;
	
	public function __construct() {
		parent::__construct();
		$this -> loadRoutes();
	}
	
	public function route($host,$path) {
		
		//Define the current app from this uri
		$this -> app = CURRENT_APP;
		//define('CURRENT_APP',$this -> app);
		
		//Remove the matched part of the uri in the app
		//$uri_part = $uri_sections['path'];
		$uri_part = $path;
		
		//Find the current app based on rules/etc.
		foreach($this -> rules as $rule) {
			if ($arr = match($rule['rule'],$host,$path)) {
				$this -> routeRule($rule,$arr);
				return;
				/*echo 'asdf';
				if ( is_numeric($rule['controller']) ) {
					$this -> setController($arr['matches'][$rule['controller']]);
				} else {
					$this -> setController($rule['controller']);
				}
				
				if ( is_numeric($rule['action']) ) {
					$this -> setMethod($arr['matches'][$rule['action']]);
				} else {
					$this -> setMethod($rule['action']);
				}
				
				try {
					//Try calling the controller
					$this -> callController($this -> controller,$this -> method, $arr['path']);
				} catch (Exception $e) {
					//If it doesn't work display an appropriate error page
					printr($e);
				}
				return;*/
			}		
		}

		//If there is no rule defined, use the default behavior
		$uri_postapp = explode('/',$uri_part);
		
		$this -> uri_parts = $uri_postapp;
		
		//Call the hooks for the uri
		$urihook = $this -> hooks -> callHook('uri');
		
		//Call the controller based on just the uri
		$this -> setController(array_shift($this -> uri_parts));
		$this -> setMethod(array_shift($this -> uri_parts));
		
		try {
			//Try calling the controller
			$this -> callController($this -> controller,$this -> method, $this -> uri_parts);
		} catch (Exception $e) {
			//If it doesn't work display an appropriate error page
			printr($e);
		}
	}
	
	protected function routeRule($rule,$arr) {
		$replace_array = array();
		foreach($arr['matches'] as $m) {
			$replace_array []= '*';
		}
		switch($rule['rtype']) {
			case Router::passThru:
				$fileto = str_replace($replace_array,$arr['matches'],$rule['path']);
				if ( substr($urlto,-1) != '/' ) {
					$fileto .= '/';
				}
				$fileto .= $arr['path'];
				$this -> passThrough($fileto);
			break;
			case Router::redirect:
				$urlto = str_replace($replace_array,$arr['matches'],$rule['url']);
				if ( substr($urlto,-1) != '/' ) {
					$urlto .= '/';
				}
				$urlto .= $arr['path'];
				$this -> forward($urlto);
			break;
			default:
			case Router::normal:
				if ( is_numeric($rule['controller']) ) {
					$this -> setController($arr['matches'][$rule['controller']]);
				} else {
					$this -> setController($rule['controller']);
				}
				
				if ( is_numeric($rule['action']) ) {
					$this -> setMethod($arr['matches'][$rule['action']]);
				} else {
					$this -> setMethod($rule['action']);
				}
				
				try {
					//Try calling the controller
					$this -> callController($this -> controller,$this -> method, $arr['path']);
				} catch (Exception $e) {
					//If it doesn't work display an appropriate error page
					printr($e);
				}
				return;
			break;
		}
	}
	
	protected function setController($controller) {
		if ( !$controller) {
			$controller = 'index';
		}
		$this -> controller = $controller;
	}
	
	protected function setMethod($method) {
		if ( !$method) {
			$method = 'index';
		}
		$this -> method = $method;
	}
	
	protected function callController($controller,$method,$params=array()) {
		
		//Call the hooks before the controller is initialized
		$prehooks = $this -> hooks -> callHook('pre_controller');
		$prehook = true;
		//If the hook returns an array AND all of them, if all are true the var will the true
		if ( is_array($prehooks) ) {
			foreach ( $prehooks as $var ) {
				$prehook = $prehook and $var;
			}
		} else {
			$prehook = $prehooks;
		}
		
		//Get the controller class name
		$controller_class = ucfirst($controller).'Controller';
		
		//Load the controller class file
		$this -> load -> file($controller_class.'.php','controllers');
		
		//Check whether the class exists that is being called
		if ( class_exists($controller_class) ) {
			$this -> controller_obj = new $controller_class;
			
			//Call the hooks after the controller is initialized but before it is executed
			$inithooks = $this -> hooks -> callHook('post_controller_constructor');
			$inithook = true;
			//If the hook returns an array AND all of them, if all are true the var will the true
			if ( is_array($inithooks) ) {
				foreach ( $inithooks as $var ) {
					$inithook = ($inithook and $var);
				}
			} else {
				$inithook = $inithooks;
			}
			//vardump($inithook);
			
			//If the hooks all return true continue calling the controller
			if ( $prehook and $inithook ) {
				
				//Check whether the requested method exists
				if ( method_exists($this -> controller_obj,$method) ) {
					//Call the controller (finally!)
					call_user_func_array(array(&$this -> controller_obj, $method),$params);
					//printr($this -> pathMatchWC('cool/beans','*/*'));
				} else {
					//If not throw a 404 exception to be caught by the calling function
					throw new Exception('Could not find '.$controller_class.'->'.$method,404);
				}
			} else {
				throw new Exception('Forbidden',500);
			}
		} else {
			//If not throw a 404 exception to be caught by the calling function
			throw new Exception('Could not find '.$controller_class.'->'.$method.'()',404);
		}
		
		$posthook = $this -> hooks -> callHook('post_controller');
	}
	
	public function bind($rule_host,$rule_path,$content,$type,$regex=false) {
		$content['rtype'] = $type;
		$content['rule'] = array('host'=>$rule_host,'path'=>$rule_path);
		$content['rule']['type'] = $regex ? REGEX : WILDCARD;
		$this -> rules[] = $content;
	}

	public function passThrough($filename) {
		//$finfo = finfo_open(FILEINFO_MIME); 
		if ( file_exists(CURRENT_APP.'/'.$filename) ) {
			$filename = CURRENT_APP.'/'.$filename;
		} else if ( file_exists('global/'.$filename) ) {
			$filename = 'global/'.$filename;
		} else {
			echo CURRENT_APP.'/'.$filename;
			echo 'global/'.$filename;
			throw new Exception('Could not find the file you were looking for',404);
		}
		header('Content-Type: '.returnMIMEType ($filename));
		readfile($filename);
		exit();
	}
	
	public function forward($url) { //Forwards to URL
		header('Location: '.$url);
		echo '<html><head></head><body>Click <a href="'.$url.'">here</a> if you are not redirected immediately...</body></html>';
	}
	
	public function loadRoutes() {
		if ( file_exists(CURRENT_APP.'/config/routes.php') ) {
			include CURRENT_APP.'/config/routes.php';
		}
		
		if ( file_exists('global/config/routes.php') ) {
			include 'global/config/routes.php';
		}
	}
}

?>