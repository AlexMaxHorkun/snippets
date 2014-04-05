<?php
namespace Service\Component\Gateway\Loader;

use Service\Component\Model\Currency;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
abstract class CurrencyRateLoader implements LoaderInterface{
	/**
	@var array of Currency.
	*/
	private $curs=array();
	/**
	@var Currency
	*/
	private $compareTo;
	
	public function __construct(array $curs=NULL,Currency $ctc=NULL){
		if($curs) $this->setCurrencyList($curs);
		if($ctc) $this->setCompareTo($ctc);
	}
	/**
	@return array of Currency.
	*/
	public function getCurrencyList(){
		return $this->curs;
	}
	
	public function setCurrencyList(array $curs){
		foreach($curs as $cur){
			if(!($cur instanceof Currency)){
				throw new \InvalidArgumentException('array must containt only Currency');
			}
		}
		$this->curs=$curs;
	}
	/**
	@return Currency
	*/
	public function getCompareTo(){
		return $this->compareTo;
	}
	
	public function setCompareTo(Currency $c){
		$this->compareTo=$c;
	}
	/**
	@return array with assoc arrays with keys `currency` and `rate`.
	*/
	abstract protected function loadRates();
	
	public function load(){
		if(!($this->getCompareTo() && $this->getCurrencyList())){
			throw new \RuntimeException('currency list and main currency to compare to needed');
		}
		$data=$this->loadRates();
		foreach($data as $row){
			if(!isset($row['currency'],$row['rate']) || !($row['currency'] instanceof Currency) || !$row['rate']){
				throw new \RuntimeException('Invalid data returned, must contain `currency` and `rate` keys');
			}
		}
		return $data;
	}
}
?>
