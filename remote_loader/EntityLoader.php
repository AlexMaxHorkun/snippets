<?php
namespace Service\Component\Gateway\Loader;

use Service\Component\Gateway\Relation;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
abstract class EntityLoader implements LoaderInterface{
	/**
	@var array Existing entity list.
	*/
	private $entityList=array();
	/**
	@var array Remote entity to local relations list.
	*/
	private $relations=array();
	/**
	Loads entity list from remote service.
	
	@return array Every row must contain `id` key - it will be a remote ID.
	*/
	abstract protected function loadData();
	/**
	Converts received data row to an entity.
	
	Compare data to existing entities, if no match - return a new one based on data.
	
	@return object;
	*/
	abstract protected function convertDataToEntity(array $data);
	/**
	Processes received from loadData method data.
	
	@return array which will be returned by load method.
	*/
	protected function processReceived(array $data){
		$data=$this->loadData();
		$objs=array();
		foreach($data as $row){
			if(!isset($row['id'])){
				throw new \RuntimeException('Data received from remote source doesn\'t have `id` key');
			}
			$obj=$this->convertDataToEntity($row);
			if(!$obj){
				throw new \RuntimeException('convertDataToEntity method returned null - that not right! It must return an object - an exisiting entity match based on data or a new one based on data');
			}
			$objs[]=$obj;
			$this->entityList[]=$obj;
			$relation=new Relation($row['id'],$obj);
			$this->relations[]=$relation;
		}
		return $objs;
	}
	/**
	@return array of Relation
	*/
	public function relations(){
		return $this->relations;
	}
	/**
	@return Relation|null
	*/
	public function findRelationByObject(object $obj){
		$rel=NULL;
		foreach($this->relations() as $relation){
			if($relation->getMatchedObject()===$obj){
				$rel=$relation;
				break;
			}
		}
		return $rel;
	}
	/**
	@return Relation|null
	*/
	public function findRelationByRemoteId($id){
		$rel=NULL;
		foreach($this->relations() as $relation){
			if($relation->getRemoteId()===$id){
				$rel=$relation;
				break;
			}
		}
		return $rel;
	}
	/**
	@return array List of exisiting entities.
	*/
	public function getEntityList(){
		return $this->entityList;
	}
	
	public function setEntityList(array $l){
		$this->entityList=$l;
	}
	
	public function load(){
		return $this->processReceived($this->loadData());
	}
}
?>
