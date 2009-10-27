<?php

class AclAuth extends Plugin {
	
	protected $hooks = array('login'=>array('Auth','authorized'));
	
	public function __construct() {
		$this -> load -> plugin('Acl');
	}
	
	public function authorized() {
		
	}
	
}

?>