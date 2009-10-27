<?php

/*
 * Base class for most modules, allows for access to enabled plugins, helpers, controllers, etc.
 * 
 * @package		Viridian
 * @author		Michael LeGore
 * @version		1.0
 * 
 */

class Base {

	protected $load;
	public $models;
	public $plugins;
	public $display;
	
	private static $_viewinst;
	private static $_instance;
	
	protected function __construct() {
		$this -> load =& Loader::instance($this);
		$this -> app = App::instance();
		$this -> models = new ArrayObject($this -> load -> getModels(),ArrayObject::ARRAY_AS_PROPS);
		$this -> plugins = new ArrayObject($this -> load -> getPlugins(),ArrayObject::ARRAY_AS_PROPS);
		$core =& $this -> load -> getCoreClasses(); 
		foreach($core as $key=>&$coreclass) {
			$this -> $key =& $coreclass;
		}
		$this -> load -> core_class('view');
		if( self::$_viewinst == null ) {
			self::$_viewinst = new View;
		}
		$this -> display =& self::$_viewinst;	
	}

	public static function & instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new Base;
		}
		return self::$_instance;
	}
	
	public function loadPlugins($app = CURRENT_APP) {
		if ( file_exists(CURRENT_APP.'/config/plugins.php') ) {
			include_once(CURRENT_APP.'/config/plugins.php');	
		}
		if ( file_exists('global/config/plugins.php') ) {
			include_once('global/config/plugins.php');
		}
	}
	
}

?>