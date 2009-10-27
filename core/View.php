<?php

class View {

	private $subviews = array();
	public function __construct() {
		
	}
	
	public function & addView($name,$tpl) {
		$view = new View;
		$this -> subviews[$name] = array($view,$tpl);
		return $view;
	}
	
	public function parse($tpl) {
		foreach($this -> subviews as $name=>&$data) {
			$this -> $name = $data[0] -> parse($data[1]);
		}
		$vars = get_object_vars($this);
		unset($vars['subviews']);
		$extension = explode('.',$tpl);
		$extension = $extension[count($extension)-1];
		$driver = $this -> getDriver($extension);
		if ( file_exists(CURRENT_APP.'/views/'.$tpl) ) {
			$tpl = CURRENT_APP.'/views/'.$tpl;
		} else if ( file_exists('global/views/'.$tpl) ) {
			$tpl = 'global/views/'.$tpl;
		}
		return $driver -> parse($tpl,$vars);
	}
	
	public function render($tpl) {
		echo $this -> parse($tpl);
	}
	
	public function getDriver($extension) {
		//If there is a specific rule governing this type of template, use that to find the proper driver
		
		//Rule matching/execution goes here :-D

		//If no rule exists we'll try to autodetect the correct driver using the naming conventions
		$classname = ucfirst($extension).'ViewDriver';
		if ( file_exists(CURRENT_APP.'/views/drivers/'.$classname.'.php') ) {
			include_once CURRENT_APP.'/views/drivers/'.$classname.'.php';
			if ( class_exists($classname) ) {
				return new $classname;
			}
		}
		if ( file_exists('global/views/drivers/'.$classname.'.php') ) {
			include_once 'global/views/drivers/'.$classname.'.php';
			if ( class_exists($classname) ) {
				return new $classname;
			}
		}
	}
	
}

?>