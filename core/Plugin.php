<?php

class Plugin extends Base {
	
	protected $pluginhooks; 
	
	public function __construct() {
		if ( ! isset($this -> pluginhooks) ) {
			$this -> pluginhooks = array();
		}
		parent::__construct();
		foreach($this -> pluginhooks as $key=>$hook) {
			if ( is_array($hook[0]) ) {
				$k = 0;
				foreach($hook as $subhook) {
					$this -> hooks -> registerHook($key,$subhook,get_class($this).($k++));
				}
			} else {
				$this -> hooks -> registerHook($key,$hook,get_class($this));
			}	
		}
		$class = strtolower(get_class($this));
		$this -> config -> loadConfig(get_class($this),$class);
		$this -> cfg = $this -> config -> $class;
	}
	
}

?>