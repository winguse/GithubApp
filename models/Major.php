<?php
require_once 'DaoBase.php';

class MajorDao extends DaoBase {
	public function __construct($pdo){
		parent::__construct($pdo);
	}
	
	public function add($major) {
		$statement = $this->pdo->prepare('INSERT INTO majors(`name`) VALUES (:name)');
		return $statement->execute(array(':name' => $major->getName()));
	}
	
	public function del($major) {
		$statement = $this->pdo->prepare('DELETE majors WHERE id = :id');
		return $statement->execute(array(':id' => $major->getId()));
	}
	
	public function find($id) {
		$statement = $this->pdo->prepare('SELECT `id`, `name` FROM majors WHERE `id` = :id');
		$status = $statement->execute(array(':id' => $id));
		if($status === FALSE) return null;
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if($result === FALSE) return null;
		$major = new Major($result['id'], $result['name']);
		return $major;
	}
	
	public function update($major) {
		$statement = $this->pdo->prepare('UPDATE majors SET `name` = :name WHERE `id` = :id');
		return $statement->execute(array(':id' => $major->getId(), ':name' => $major->getName()));
	}
	
	public function getAll(){
	    $result = array();
		$statement = $this->pdo->prepare('SELECT `id`, `name` FROM majors');
		$statement->execute();
		while($row = $statement->fetch()){
		    $result[] = new Major($row['id'], $row['name']);
		}
		return $result;
	}
	
}

/*
 * Major DTO
 */
class Major {
	private $id;
	private $name;
	
	public function __construct($id = null, $name = null){
		$this->id = $id;
		$this->name = $name;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getid(){
		return $this->id;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
}