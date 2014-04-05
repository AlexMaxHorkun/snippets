<?php
namespace Service\Component\Gateway\Loader;

use Service\Component\Model\Currency;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
abstract class CurrencyLoader extends EntityLoader{
	public function setEntityList(array $l){
		foreach($l as $e){
			if(!($e instanceof Currency)){
				throw new \InvalidArgumentException('Given array must contain only currencies');
			}
		}
		parent::setEntityList($l);
	}
	
	protected function processReceived(array $data){
		$objs=parent::processReceived($data);
		foreach($objs as $o){
			if(!($o instanceof Currency)){
				throw new \RuntimeException('Data received by loading from remote source must be converted to Currency objs');
			}
		}
		return $objs;
	}
	
	protected function convertDataToEntity(array $data){
		if(!isset($data['name'])){
			throw new \InvalidArgumentException('Given array must contain `name` key');
		}
		$cur=NULL;
		foreach($this->getEntityList() as $c){
			if($data['name']==$c->getName()){
				$cur=$c;
				break;
			}
		}
		if(!$cur){
			$cur=new Currency(array('name'=>$data['name']));
			if(isset($data['alias'])) $cur->setShortName($data['alias']);
		}
		return $cur;
	}
}
?>
