<?php
namespace Service\Component\Model;

use Doctrine\Common\Collections\ArrayCollection as Collection;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
class Currency extends Component{
	/**
	@var string
	*/
	protected $shortName;
	/**
	@var string
	*/
	/**
	@var bool
	*/
	protected $main=FALSE;
	/**
	@var float
	*/
	protected $factor=1;
	/**
	@var Collection of State.
	*/
	protected $states;
	/**
	@var array of Currency.
	*/
	protected static $curs=array();
	
	public function __construct(array $data=NULL){
		$this->states=new Collection();
		parent::__construct($data);
	}
	/**
	@throws \InvalidArgumentException If given currency already in array.
	
	@return void
	*/
	public static function addCurrency(Currency $c){
		foreach(self::$curs as $cur){
			if($cur->getId()==$c->getId() && $cur->getId()){
				throw new \InvalidArgumentException('Currency with such ID already exists');
			}
		}
		
		self::$curs[]=$c;
		if($c->isMain()){
			self::setMainCurrency($c);
		}
	}
	/**
	@return array of Currency.
	*/
	public static function currencies(){
		return self::$curs;
	}
	/**
	Sets main currency.
	
	If there are any other curs with 'main' prop = TRUE it will be set to FALSE.
	
	@return void
	*/
	protected static function setMainCurrency(Currency $c){
		foreach(self::$curs as $cur){
			if($c->getId()){
				if($c->getId()!=$cur->getId()){
					$cur->setMain(FALSE);
					continue;
				}
			}
			elseif($c!==$cur){
				$cur->setMain(FALSE);
			}
		}
	}
	/**
	@return Currency|null
	*/
	public static function main(){
		foreach(self::$curs as $cur){
			if($cur->isMain()){
				return $cur;
			}
		}
		return NULL;
	}
	
	public function setShortName($name){
		$this->shortName=$name;
	}
	/**
	@return string
	*/
	public function getShortName(){
		return $this->shortName;
	}
	/**
	@return string Short name, or name if Short name is empty.
	*/
	public function __toString(){
		if(mb_strlen($this->getShortName())){
			return $this->getShortName();
		}
		return parent::__toString();
	}
	/**
	Set currency to main others to be compared to.
	
	@param bool
	
	@return void
	*/
	public function setMain($is){
		$is=(bool)$is;
		if($is!=$this->main){
			$this->main=$is;
			if($is){
				self::setMainCurrency($this);
			}
		}
	}
	/**
	Is this is main cur others to compare to.
	
	@return bool
	*/
	public function isMain(){
		return $this->main;
	}
	/**
	Factor comparing to main cur.
	
	@param float Greater then 0.
	
	@return void
	*/
	public function setFactor($f){
		$f=(float)$f;
		if(!$f){
			$f=1;
		}
		$this->factor=$f;
	}
	/**
	Factor comparing to main cur.
	
	@return float
	*/
	public function getFactor(){
		return $this->factor;
	}
	/**
	Converts given number (in this currency) to value in given currency.
	
	@param float Price.
	@param Currency
	
	@throws \RuntimeException If no main currency set.
	
	@return float
	*/
	public function convertTo($p,$c){
		if(!self::main()){
			throw new \RuntimeException('No main currency available');
		}
		if(!$this->main){
			$p/=$this->getFactor();
		}
		if(!$c->isMain()){
			$p*=$c->getFactor();
		}
		return $p;
	}
	/**
	Converts given number (in given currency) to value in this currency.
	
	@param float Price.
	@param Currency
	
	@throws \RuntimeException If no main currency set.
	
	@return float
	*/
	public function convertFrom($p,$c){
		if(!self::main()){
			throw new \RuntimeException('No main currency available');
		}
		if($c===$this){
			return $p;
		}
		if(!$c->isMain()){
			$p/=$c->getFactor();
		}
		if(!$this->main){
			$p*=$this->getFactor();
		}
		
		return $p;
	}
	/**
	@return array of State this cur belongs.
	*/
	public function getStates(){
		return $this->states->toArray();
	}
	
	public function addState(State $s){
		$this->states[]=$s;
		if(!in_array($this,$s->getCurrencies(),TRUE)){
			$s->addCurrency($this);
		}
	}
	/**
	Adds array of State to existsting one.
	
	@param array of State.
	*/
	public function addStates(array $ss){
		foreach($ss as $s){
			$this->addState($s);
		}
	}
	/**
	Replaces existing arrays of State to new one.
	
	@param array of State. 
	*/
	public function setStates(array $ss){
		$this->removeStates();
		$this->addStates($ss);
	}
	
	public function removeState(State $s){
		$this->states->removeElement($s);
		if(in_array($this,$s->getCurrencies(),TRUE)){
			$s->removeCurrency($this);
		}
	}
	/**
	Removes all states.
	
	@return void
	*/
	public function removeStates(){
		foreach($this->states as $st){
			$this->removeState($st);
		}
	}
}
?>
