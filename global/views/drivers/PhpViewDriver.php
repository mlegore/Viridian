<?php

class PhpViewDriver {
	
	public function parse($tpl,$vars) {
		foreach($vars as $key=>$value) {
			$this -> $key = $value;	
		}
		unset($vars);
		include $tpl;
	}
	
}

?>