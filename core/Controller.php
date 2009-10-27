<?php

/*
 * This defines the base controller class from which all other controllers extend
 * 
 * @package		Viridian
 * @author		Michael LeGore
 * @version		1.0
 * 
 */

class Controller extends Base {
	
	protected $uses_plugins = array();
	protected $core = array();
	
	public function __construct() {
		parent::__construct();
		foreach ( $this -> core as $class ) {
			$this -> load -> core_class($class);
		}
		foreach ( $this -> uses_plugins as $plugin ) {
			$this -> load -> plugin($plugin);
		}
		
	}
	
}

?>