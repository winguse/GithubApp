<?php

class Dao {
	protected $pdo;
	
	public function __construct($pdo){
		$this->pdo = $pdo;
		$this->propertyNamesCache = array(); 
	}
	
	public function add($obj_array) {
		if(!is_array($obj_array)) $obj_array = array($obj_array);
		$ref = new ReflectionClass($obj_array[0]);
		$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
		$propertyNames = array();
		foreach($properties as $property){
			if($property->getName() == 'id') continue; // id is primary key skip
			$propertyNames[] = $property->getName();
		}
		$sql = 'INSERT INTO ' . $ref->getName() . '(`' . implode('`, `', $propertyNames) . '`) VALUES (:' . implode(', :', $propertyNames) . ')';
		$statement = $this->pdo->prepare($sql);
		$ret = 0;
		foreach($obj_array as $obj){
			$values = array();
			foreach($properties as $property){
				if($property->getName() == 'id') continue;
				$values[':' . $property->getName()] = $property->getValue($obj);
			}
			if(!$statement->execute($values)) $ret++;
		}
		return $ret;
	}
	
	public function delete($obj_array) {
		if(!is_array($obj_array)) $obj_array = array($obj_array);
		$statement = $this->pdo->prepare('DELETE FROM ' . get_class($obj_array[0]) . ' WHERE id = :id');
		$ret = 0;
		foreach($obj_array as $obj)
			if(!$statement->execute(array(':id' => $obj->id)))
				$ret++;
		return $ret;
	}
	
	public function update($obj_array) {
		if(!is_array($obj_array)) $obj_array = array($obj_array);
		$ref = new ReflectionClass($obj_array[0]);
		$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
		$sql = 'UPDATE ' . $ref->getName() . ' SET ';
		$add_comma = false;
		foreach($properties as $property){
			if($property->getName() == 'id') continue; // id is primary key skip
			if($add_comma) $sql .= ', ';
			$sql .= '`' . $property->getName() . '` = :' . $property->getName();
			$add_comma = true;
		}
		$sql .= ' WHERE id = :id';
		//echo "in update sql=".$sql;//没有输出？？？
		$statement = $this->pdo->prepare($sql);
		$ret = 0;
		foreach($obj_array as $obj){
			$values = array();
			foreach($properties as $property){
				$values[':' . $property->getName()] = $property->getValue($obj);
			}
			if(!$statement->execute($values)) $ret++;
		}
		//echo "in update function";
		return $ret;
	}
	
	public function find($filter_obj, $condition = '='){
		$ref = new ReflectionClass($filter_obj);//用反向工程的好处？？？？
		$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
		$sql = 'SELECT ';
		$where = '';
		$add_comma = false;
		$query_parameters = array();
		foreach($properties as $property) {
			if($add_comma) $sql .= ', ';
			$sql .= '`' . $property->getName() . '`';
			$add_comma = true;
			$value = $property->getValue($filter_obj);
			if($value === NULL) continue;
			$query_parameters[$property->getName()] = $value;
			$where .= ' `' . $property->getName() . '` ' . $condition . ' :' . $property->getName();
		}
		$sql .= ' FROM ' . $ref->getName();
		if($where != '') {
			$sql .= ' WHERE' . $where;
		}
		//echo "in pdo find sql=".$sql;
		$statement = $this->pdo->prepare($sql);
		$statement->execute($query_parameters);
		$result = array();
		while($row = $statement->fetch()){
		    $obj = $ref->newInstance();
		    foreach($properties as $property) {
		    	$property->setValue($obj, $row[$property->getName()]);
		    }
		    $result[] = $obj;
		}
		return $result;
	}
}