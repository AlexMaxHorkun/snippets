<?php
namespace Service\Component\Model;

use Doctrine\Common\Collections\ArrayCollection as DoctrineCollection;
use Service\Model\Service;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
class State extends Component{
	/**
	@var DoctrineCollection of Region.
	*/
	protected $regions;
	/**
	@var DoctrineCollection of Currency.
	*/
	protected $curs;
	
	public function __construct(array $data=null){
		$this->regions=new DoctrineCollection;
		$this->curs=new DoctrineCollection();
		parent::__construct($data);		
	}
	/**
	@param DoctrineCollection
	*/
	public function setRegions(DoctrineCollection $regs){
		if(count($this->regions->toArray())){
			foreach($this->regions as $key=>$val){
				$this->regions[$key]->setState(NULL);
			}
		}
		foreach($regs as $key=>$reg){
			$regs[$key]->setState($this);
		}
		$this->regions=$regs;
	}
	/**
	@param Region
	*/
	public function addRegion(Region $reg){
		$reg->setState($this);
		if(!in_array($reg,$this->regions->toArray())){
			$this->regions[]=$reg;
		}
	}
	/**
	@return array
	*/
	public function getRegions(){
		return $this->regions->toArray();
	}
	
	public function removeRegion(Region $reg){
		if(in_array($reg,$this->regions->toArray())){
			unset($this->regions[array_search($reg,$this->regions->toArray())]);
			//$reg->setState(NULL);
		}
	}
	/**
	@param int|string Id or Name of the Region.
	
	@return Region
	*/
	public function getRegion($id){
		$id=(string)$id;
		if((int)$id != $id){
			$name=$id;
			unset($id);
		}
		foreach($this->regions as $reg){
			if(isset($name)&&$reg->getName()==$name){
				return $reg;
			}
			else{
				if($reg->getId()==$id){
					return $reg;
				}
			}
		}
		
		return NULL;
	}
	/**
	@return array of Service.
	*/
	public function getServices(){
		$ss=array();
		foreach($this->regions as $r){
			array_merge($ss,$r->getServices());
		}
		return $ss;
	}
	/**
	@return bool
	*/
	public function hasServices(){
		foreach($this->regions as $r){
			if($r->hasServices()){
				return TRUE;
			}
		}
		return FALSE;
	}
	/**
	@return array of Currency this state own.
	*/
	public function getCurrencies(){
		return $this->curs->toArray();
	}
	
	public function addCurrency(Currency $c){
		$this->curss[]=$c;
		if(!in_array($this,$c->getStates(),TRUE)){
			$c->addState($this);
		}
	}
	/**
	Adds array of Currency to existsting one.
	
	@param array of Currency.
	*/
	public function addCurrencies(array $cs){
		foreach($cs as $c){
			$this->addCurrency($s);
		}
	}
	/**
	Replaces existing arrays of Currency to new one.
	
	@param array of Currency. 
	*/
	public function setCurrencies(array $cs){
		$this->removeCurrencies();
		$this->addCurrencies($cs);
	}
	
	public function removeCurrency(Currency $c){
		$this->curs->removeElement($c);
		if(in_array($this,$c->getStates(),TRUE)){
			$c->removeState($this);
		}
	}
	/**
	Removes all currencies.
	*/
	public function removeCurrencies(){
		foreach($this->curs as $c){
			$this->removeCurrency($c);
		}
	}
}
?>
