<?php
namespace Service\Component\Gateway\Loader;

use Service\Component\Model\State;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
abstract class StateLoader extends EntityLoader{
	public function setEntityList(array $l){
		foreach($l as $e){
			if(!($e instanceof State)){
				throw new \InvalidArgumentException('Given array must contain only states');
			}
		}
		parent::setEntityList($l);
	}
	
	protected function processReceived(array $data){
		$objs=parent::processReceived($data);
		foreach($objs as $o){
			if(!($o instanceof State)){
				throw new \RuntimeException('Data received by loading from remote source must be converted to State objs');
			}
		}
		return $objs;
	}
	
	protected function convertDataToEntity(array $data){
		if(!isset($data['name'])){
			throw new \InvalidArgumentException('Given array must contain `name` key');
		}
		$state=NULL;
		foreach($this->getEntityList() as $st){
			if($data['name']==$st->getName()){
				$state=$st;
				break;
			}
		}
		if(!$state){
			$state=new State(array('name'=>$data['name']));
		}
		return $state;
	}
}
?>
