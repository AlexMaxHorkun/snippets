<?php
namespace Service\Component\Gateway;

use AbstractGateway as Gateway;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
class Relation{
	/**
	@var int
	*/
	private $remoteId=0;
	/**
	@var Gateway
	*/
	private $gateway;
	/**
	@var object entity matched.
	*/
	private $object;
	
	public function __construct($remoteId=NULL,$obj=NULL){
		if($remoteId) $this->setRemoteid($remoteId);
		if($obj) $this->setMatchedObject($obj);
	}
	/**
	@return int
	*/
	public function getId(){
		return $this->id;
	}
	/**
	@return int Remote gateway resource ID.
	*/
	public function getRemoteId(){
		return $this->remoteId;
	}
	/**
	@param int
	*/
	public function setRemoteId($id){
		$this->removeId=(int)$id;
	}
	/**
	@return object matched.
	*/
	protected function getMatchedObject(){
		return $this->object;
	}
	
	public function setMatchedObject($obj){
		$this->object=$obj;
	}
	/**
	@return Gateway
	*/
	public function getGateway(){
		return $this->gateway;
	}
	
	public function setGateway(Gateway $gt){
		$this->gateway=$gt;
	}
}
?>
