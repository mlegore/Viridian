<?php

/* 
 * Implements an activerecord-like model for access of model data
 * 
 * @package		Viridian
 * @author		Michael LeGore
 * @version		1.0
 * 
 */

class Model extends Base {
	
	private $has_many = array();
		
	public function __construct($id,$table) {
		$data = $this -> db -> getTableData($table);
		$data = $this -> db -> select($table,'*') -> where('id',$id) -> exec();
		foreach($data as $key=>$value) {
			$this -> $key = $value;
		}
		
	}
	public function commit() {
		$query = 'update '.$table.' set ';
		foreach($this -> tableData['fields'] as $field) {
			$query .= $field.'='.$this -> db -> quote($this -> $field).',';
		}
		$query = substr($query,0,count($query)-1);	
		$query .= ' where id='.$this -> id;
		$this -> db -> query($query);
	}
	
	public function __destruct() {
		$this -> commit();
	}
	
}

?>