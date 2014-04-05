<?php
namespace Application\CMS;

/**
Block container.

@author Alex Horkun mindkilleralexs@gmail.com
*/
class Blocks implements \ArrayAccess,\Countable, \IteratorAggregate{
	/**
	@var array Of Block.
	*/
	private $blocks=array();
	/**
	@param array|null Of Block.
	*/
	public function __construct(array $bs=NULL){
		if($bs){
			$this->addAll($bs);
		}
	}
	
	public function add(Block $b){
		if(!in_array($b,$this->blocks)){
			$this->blocks[]=$b;
		}
	}
	/**
	@param array Of Block.
	*/
	public function addAll(array $bs){
		foreach($bs as $b){
			$this->add($b);
		}
	}
	/**
	@return array Of Block.
	*/
	public function blocks(){
		return $this->blocks;
	}
	/**
	@param int ID.
	
	@return Block|null With such ID.
	*/
	public function findById($id){
		foreach($this->blocks as $b){
			if($b->getId()==$id){
				return $b;
			}
		}
		return NULL;
	}
	/**
	@return bool
	*/
	public function has(Block $b){
		return in_array($b,$this->blocks,TRUE);
	}
	/**
	Removes block from array.
	
	@return void
	*/
	public function remove(Block $b){
		if($this->has($b)){
			unset($this->blocks[array_search($b,$this->blocks)]);
		}
	}
	/**
	Removes all blocks.
	*/
	public function clear(){
		$this->blocks=array();
	}
	
	//ArrayAcces
	public function offsetExists ($offset){
		return isset($this->blocks[$offset]);
	}
	
	public function offsetGet ( $offset ){
		if(isset($this->blocks[$offset])){
			return $this->blocks[$offset];
		}
		return NULL;
	}
	/**
	Forbidden.
	
	@throws \BadMethodCallException
	*/
	public function offsetSet ( $offset ,  $value ){
		throw new \BadMethodCallException('Forbidden');
	}
	/**
	@throws \InvalidArgumentException If offset does not exist.
	*/
	public function offsetUnset ( $offset ){
		if(isset($this->blocks[$offset])){
			$this->remove($this->blocks[$offset]);
		}
		else{
			throw new \InvalidArgumentException('Invalid offset');
		}
	}
	//Countable
	public function count(){
		return count($this->blocks);
	}
	//IteratorAggregate
	public function getIterator(){
		return new \ArrayIterator($this->blocks);
	}
}
?>
