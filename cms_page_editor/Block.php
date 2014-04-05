<?php
namespace Application\CMS;

/**
Page block.

Template file is being read once, to refresh text use refresh method, autosave is enabled by default.

@author Alex Horkun mindkilleralexs@gmail.com
*/
class Block{
	/**
	@var int
	*/
	private $id=0;
	/**
	@var string
	*/
	private $name="";
	/**
	@var string File name.
	*/
	private $template=NULL;
	/**
	@var string
	*/
	private $description="";
	/**
	@var string
	*/
	private $text="";
	/**
	@var bool Save on setText?.
	*/
	private $autosave=TRUE;
	/**
	@param string $name Block name.
	@param string $t Template file name.
	*/
	public function __construct($id=NULL,$name=NULL,$t=NULL, $d=NULL){
		if($id){
			$this->setId($id);
		}
		if($name){
			$this->setName($name);
		}
		if($t){
			$this->setTemplate($t);
		}
		if($d){
			$this->setDescription($d);
		}
	}
	/**
	@param int Not zero.
	*/
	public function setId($id){
		if($id){
			$this->id=(int)$id;
		}
		else{
			throw new \InvalidArgumentException('ID cannot be zero');
		}
	}
	/**
	@return int
	*/
	public function getId(){
		return $this->id;
	}
	/**
	@param string
	*/
	public function setName($n){
		$n=(string)$n;
		$this->name=$n;
	}
	/**
	@return string
	*/
	public function getName(){
		return $this->name;
	}
	/**
	@param string
	*/
	public function setDescription($d){
		$this->description=(string)$d;
	}
	/**
	@return string
	*/
	public function getDescription(){
		return $this->description;
	}
	/**
	@param string Template file name.
	
	@throws \InvalidArgumentException If file does not exist.
	*/
	public function setTemplate($t){
		$t=(string)$t;
		if(file_exists($t)){
			$this->template=$t;
		}
		else{
			throw new \InvalidArgumentException('Invalid argument given - file "'.$t.'" does not exist');
		}
	}
	/**
	@return string
	*/
	public function getTemplate(){
		return $this->template;
	}
	/**
	Defines if template text will be saved on setText.
	
	@param bool
	*/
	public function setAutosave($enable=TRUE){
		$this->autosave=(bool)$enable;
	}
	/**
	Defines if template text will be saved on setText.
	
	@return bool
	*/
	public function isAutosave(){
		return $this->autosave;
	}
	/**
	Saves text to template file.
	
	@throws \RuntimeException If saving failed.
	@throws \RuntimeException If template file name is undefined.
	
	@return void
	*/
	public function save(){
		if(!$this->template){
			throw new \RuntimeException('Template file name is undefined, cannot save changes');
		}
		$res=file_put_contents($this->template,$this->text);
		if($res===FALSE){
			throw new \RuntimeException('Error on writing changes to file "'.$this->template.'"');
		}
	}
	/**
	Reads template text, saves it in text attr.
	
	@throws \RuntimeException If template file name is not set.
	@throws \RuntimeExcpetion If there was error on file read.
	
	@return string
	*/
	protected function readText(){
		if(!$this->template){
			throw new \RuntimeException('Template file name is undefined, cannot read it\'s content');
		}
		$res=file_get_contents($this->template);
		if($res===FALSE){
			throw new \RuntimeException('Error on reading template file');
		}
		$this->text=(string)$res;
		return $this->text;
	}
	/**
	@return string Template file content.
	*/
	public function getText(){
		if(!$this->text){
			$this->readText();
		}
		return $this->text;
	}
	/**
	Will be saved if autosave is enabled.
	
	@param string
	*/
	public function setText($t){
		$this->text=(string)$t;
		if($this->autosave){
			$this->save();
		}
	}
	/**
	Rereads template text.
	
	@return void
	*/
	public function refresh(){
		$this->readText();
	}
}
?>
