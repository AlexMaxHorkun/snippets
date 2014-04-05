<?php
namespace Service\Component\Gateway;

use Service\Component\Gateway\Loader\StateLoader;
use Service\Component\Gateway\Loader\CurrencyLoader;
use Service\Component\Gateway\Loader\CurrencyRateLoader;
/**
@author Alexander Horkun mindkilleralexs@gmail.com
*/
class Gateway{	
	/**
	@var StateLoader
	*/
	private $stateLoader;
	/**
	@var CurrencyLoader
	*/
	private $currencyLoader;
	/**
	@var CurrencyRateLoader
	*/
	private $currencyRateLoader;

	public function __construct(StateLoader $sl,CurrencyLoader $cl, CurrencyRateLoader $crl){
		if($sl) $this->setStateLoader($sl);
		if($cl) $this->setCurrencyLoader($cl);
		if($crl) $this->setCurrencyRateLoader($crl);
	}
	/**
	@return array of Model\State.
	*/
	public function getStates(){
		return $this->getStateLoader()->getEntityList();
	}
	/**
	@param array of Model\State.
	*/
	public function setStates(array $states){
		$this->getStateLoader()->setEntityList($states);
	}
	/**
	@return array f Model\Currency.
	*/
	public function getCurrencies(){
		return $this->getCurrencyLoader()->getEntityList();
	}
	/**
	@param array of Model\Currency.
	*/
	public function setCurrencies(array $curs){
		$this->getCurrencyLoader()->setEntityList($curs);
		$this->getCurrencyRateLoader()->setCurrencyList($curs);
	}
	/**
	@return array of StateRelation.
	*/
	public function stateRelations(){
		return $this->getStateLoader()->relations();
	}
	/**
	@return array of CurrencyRelation.
	*/
	public function currencyRelations(){
		return $this->getCurrencyLoader()->relations();
	}
	
	public function loadStates(){
		return $this->getStateloader()->load();
	}
	
	public function loadCurrencies(){
		return $this->getCurrencyLoader()->load();
	}
	/**
	@return StateLoader
	*/
	public function getStateLoader(){
		return $this->stateLoader;
	}
	
	public function setStateLoader(StateLoader $sl){
		$this->stateLoader=$sl;
	}
	/**
	@return CurrencyLoader
	*/
	public function getCurrencyLoader(){
		return $this->currencyLoader;
	}
	
	public function setCurrencyLoader(CurrencyLoader $cl){
		$this->currencyLoader=$cl;
	}
	/**
	@return CurrencyRateLoader
	*/
	public function getCurrencyRateLoader(){
		return $this->currencyRateLoader;
	}
	
	public function setCurrencyRateLoader(CurrencyRateLoader $crl){
		$this->currencyRateLoader=$crl;
	}
	
	public function loadCurrencyRates(){
		return $this->getCurrencyRateLoader()->load();
	}
}
?>
