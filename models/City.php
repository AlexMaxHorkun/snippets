<?php
namespace Service\Component\Model;

use Service\Model\Service;
use Doctrine\Common\Collections\ArrayCollection as DoctrineCollection;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
class City extends Component{
	/**
	@var Region
	*/
	protected $region;
	/**
	@var array of Service.
	*/
	protected $services;
	
	public function __construct(array $data=NULL){
		$this->services=new DoctrineCollection();
		parent::__construct($data);
	}
	
	public function setRegion(Region $reg){
		$this->region=$reg;
	}
	/**
	@return Region
	*/
	public function getRegion(){
		return $this->region;
	}
	/**
	@return int Index.
	*/
	protected function indexOfService(Service $s){
		foreach($this->services as $key=>$serv){
			if(($s->getId() && $serv->getId()==$s->getId()) || $s===$serv){
				return $key;
			}
		}
		
		return -1;
	}
	/**
	@throws \InvalidArgumentException If given service's city attr is not this city.
	*/
	public function addService(Service $s){
		if($s->getCity()!==$this){
			throw \InvalidArgumentException('Given service\'s city attribute != this');
		}
		if(!$this->hasService($s)){
			$this->services[]=$s;
		}
	}
	/**
	@return bool If Service's array is not empty.
	*/
	public function hasServices(){
		return !empty($this->services);
	}
	/**
	@return bool If having given service.
	*/
	public function hasService(Service $s){
		if(($ind=$this->indexOfService($s))!=-1){
			return TRUE;
		}
		return FALSE;
	}
	/**
	@return array of Service
	*/
	public function getServices(){
		return $this->services->toArray();
	}
	/**
	Removes service from array.
	*/
	public function removeService(Service $s){
		$ind=$this->indexOfService($s);
		if($ind!=-1){
			unset($this->services[$ind]);
		}
	}
}
?>
