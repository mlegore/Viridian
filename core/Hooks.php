<?php

class Hooks {
	
	protected $hooks = array();
	protected $acceptable_hooks = array();
	
	public function __construct() {
		
	}
	
	public function registerHook($name,$callback,$calling=NULL) {
		if ( !isset($this -> hooks[$name]) ) {
			$this -> hooks[$name] = array();
		}
		if( $calling != NULL ) {
			$this -> hooks[$name][$calling] = $callback;
		} else {
			$this -> hooks[$name][] = $callback;
		}
	}
	
	public function callHook($name,$params=array()) {
		$return = array();
		if ( ! isset($this -> hooks[$name]) ) {
			return array(true);
		}
		foreach($this -> hooks[$name] as $calling=>&$hook) {
			//printr($hook[0]);
			$return[$calling] = call_user_func_array($hook,$params);
		}
		return $return;
	}
	
}

?>