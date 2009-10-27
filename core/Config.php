<?php

class Config extends Base {
	
	public function __construct() {
		parent::__construct();
	}
	
	function loadConfig($file,$sub = null) {
		if ( $sub != null ) {
			$this -> $sub = $this -> load -> core_class('config',true);
			$this -> $sub -> loadConfig($file);
		} else {
			if ( file_exists('global/config/'.$file.'.php') ) {
				include 'global/config/'.$file.'.php';
			}
			if ( file_exists(CURRENT_APP.'/config/'.$file.'.php' ) ) {
				include CURRENT_APP.'/config/'.$file.'.php';
			}
		}
	}
		
}

?>