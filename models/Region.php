<?php
namespace Service\Component\Model;

use Doctrine\Common\Collections\ArrayCollection as DoctrineCollection;
use Service\Model\Service;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
class Region extends Component{
	/**
	@var DoctrineCollection
	*/
	protected $cities;
	/**
	@var State
	*/
	protected $state;
	
	public function __construct(array $data=null){
		$this->cities=new DoctrineCollection;
		parent::__construct($data);		
	}
	
	public function addCity(City $city){
		$city->setRegion($this);
		if(!in_array($city,$this->cities->toArray())){
			$this->cities[]=$city;
		}
	}
	
	public function setCities(DoctrineCollection $cities){
		if(count($this->cities->toArray())){
			foreach($this->cities as $key=>$val){
				$this->cities[$key]->setRegion(NULL);
			}
		}
		$this->cities=$cities;
		foreach($this->cities as $key=>$val){
			$this->cities[$key]->setRegion($this);
		}
	}
	/**
	@return array of City.
	*/
	public function getCities(){
		return $this->cities->toArray();
	}
	
	public function setState(State $st){
		if($this->state){
			$this->state->removeRegion($this);
		}
		$this->state=$st;
	}
	/**
	@return State
	*/
	public function getState(){
		return $this->state;
	}
	/**
	@return array of Service.
	*/
	public function getServices(){
		$ss=array();
		foreach($this->cities as $c){
			array_merge($ss,$c->getServices());
		}
		return $ss;
	}
	/**
	@return bool
	*/
	public function hasServices(){
		foreach($this->cities as $c){
			if($c->getServices()){
				return TRUE;
			}
		}
		return FALSE;
	}
}
?>
