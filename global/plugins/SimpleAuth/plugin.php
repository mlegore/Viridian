<?php

/*
 * SimpleAuth Class
 * 
 * An example plugin that implements a simple login scheme that works only with config files for users/passwords, but it does have the ability to have multiple users (wahoo)
 * 
 * @package		Catalyst PHP Framework
 * @author		Michael LeGore
 * @version		1.0
 *  
 */

include_once 'SecureController.php';

class SimpleAuth extends Plugin {
	
	public function __construct() {
		$this -> pluginhooks = array('post_controller_constructor'=>array($this,'authorized'),'pre_controller'=>array($this,'login'));
		parent::__construct();
	}
	
	public function authorized() {
		$controller = $this -> router -> controller_obj;
		//vardump(!is_subclass_of($controller,'SecureController') or (isset($_SESSION['username']) and isset($_SESSION['password']) and isset($_SESSION['ison']) and array_key_exists($_SESSION['username'],$this -> config -> users) and $_SESSION['password'] == $this -> config -> users[$_SESSION['username']] and $_SESSION['ison']));
		return !is_subclass_of($controller,'SecureController') or (isset($_SESSION['username']) and isset($_SESSION['password']) and isset($_SESSION['ison']) and array_key_exists($_SESSION['username'],$this -> cfg -> users) and $_SESSION['password'] == $this -> cfg -> users[$_SESSION['username']] and $_SESSION['ison']);
	}
	
	public function login() {
		if ( isset($_POST['username']) and isset($_POST['password']) ) {
			if ( array_key_exists($_POST['username'],$this -> cfg -> users) and $this -> cfg -> users[$_POST['username']] == $_POST['password'] ) {			
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['password'] = $_POST['password'];
				$_SESSION['ison'] = true;
			} else {
				$_SESSION['ison'] = false;
			}
		}
	}
	
	public function logout() {
		unset($_SESSION['ison']);
		unset($_SESSION['username']);
		unset($_SESSION['password']);
	}
	
}

?>