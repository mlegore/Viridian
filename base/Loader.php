<?php

/* Loading class
 * 
 * The loading class controls the dynamic loading of plugins, helpers, models, etc.
 * 
 * @package		Viridian
 * @author		Michael LeGore
 * @version		1.0
 * 
 */

final class Loader {
	
	private $models = array();
	private $plugins = array();
	private $core = array();
	private static $instance;
	private $instances = array();
	
	private function __construct() {
	
	}
	
	/*
	 * This returns and instance of the loader class, and should be used instead of creating a new loader
	 *
	 */
	
	public static function & instance(&$class = null) {
		if ( self::$instance == null ) {
			self::$instance = new Loader;
		}
		if ($class != null) {
			self::$instance -> instances []= &$class;
		}
		return self::$instance;	
	}
	
	public function helper($name) {
		$ucname = ucfirst(strtolower($name));
		$this -> file($ucname.'Helper.php','helpers');
	}
	
	public function & model($name) {
		$name = strtolower($name);
		$classname = ucfirst($name);
		if ( array_key_exists($name,$this -> models) ) {
			return $this -> models[$name];
		}
		if( $this -> file($name.'.php','models') ) {
			if ( class_exists($classname) ) {
				$this -> models[$name] = new $classname;
				$this -> propagate('model',$name,$this -> model[$name]);
				return $this -> models[$name];
			}
		}
	}
	
	public function & plugin($name) {
		$name = strtolower($name);
		$classname = ucfirst($name);
		if ( array_key_exists($name,$this -> plugins) ) {
			return $this -> plugins[$name];
		}
		if ( $this -> file('plugin.php','plugins/'.$name) ) {
			if ( class_exists($classname) ) {
				$this -> plugins[$name] = new $classname;
				$this -> propagate('plugin',$name,$this -> plugins[$name]);
				return $this -> plugins[$name];
			}
		}
	}
	
	public function file($filename,$path,$app=CURRENT_APP,$once = true) {
		if ( is_dir($app.'/'.$path) and file_exists($app.'/'.$path.'/'.$filename) ) {
			if ( $once ) {
				include_once($app.'/'.$path.'/'.$filename);
			} else {
				include($app.'/'.$path.'/'.$filename);
			}
			return 'app';
		}
		if ( is_dir('global/'.$path) and file_exists('global/'.$path.'/'.$filename) ) {
			if ( $once ) {
				include_once('global/'.$path.'/'.$filename);
			} else {
				include('global/'.$path.'/'.$filename);
			}
			return 'global';
		}
		if ( file_exists('core/'.$filename) and substr($path,0,6) == 'extend') {
			if ( $once ) {
				include_once('core/'.$filename);
			} else {
				include('core/'.$filename);
			}
			return true;
		}
		return false;
	}
	
	public function & core_class($name,$new_instance = false) {
		if(!$new_instance and array_key_exists($name,$this -> core)) {
			return $this -> core[$name];
		}
		$new = !array_key_exists($name,$this -> core);
		$classname = ucfirst($name);
		if ( $type = $this -> file($classname.'.php','extend') ) {
			$class = $classname;
			if ( ! ($type === true) ) {
				$class = ucfirst($type).$classname;
			}
			if ( class_exists($classname) ) {
				$instance = new $class;
			}
		}
		if ( $new ) {
			$this -> core[$name] =& $instance;
			$this -> propagate('core',$name,$instance);
		}

		return $instance;
	}
	
	public function & getModels() {
		return $this -> models;	
	}
	
	public function & getPlugins() {
		return $this -> plugins;	
	}
	
	public function & getCoreClasses() {
		return $this -> core;
	}
	private function propagate($type,$name,&$class) {
		switch($type) {
			//Core classes are accessible through their names directly
			case 'core':
			//Plugins extend the core and therefore should look the same in the code
			case 'plugin':
				foreach($this -> instances as &$i) {
					$i -> $name =& $class;
				}
			break;
			//Models are accessible through the $this -> models -> name syntax
			case 'model':
				foreach($this -> instances as &$i) {
					$i -> models -> $name =& $class;
				}
			break;
		}
	}
}

?>