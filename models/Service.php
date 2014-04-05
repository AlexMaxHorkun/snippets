<?php
namespace Service\Model;

use Service\Component as Components;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection as DoctrineCollection;
use User\Model\ServiceProvider as Provider;
use User\Model\Visitor;

/**
Provided by ServiceProvider.

@author Alexander Horkun mindkilleralexs@gmail.com
*/
class Service extends \Application\Model\AutoConstruct{
	/**
	@var int Images' width.
	*/
	static private $IMG_WIDTH=400;
	/**
	@var int Images' height.
	*/
	static private $IMG_HEIGHT=400;
	/**
	@param int Width.
	@param int Height.
	
	@throws \InvalidArgumentException If width or height is 0.
	*/
	public static function setImagesSize($w,$h){
		if(!($w&&$h)){
			throw new \InvalidArgumentException('Width and height cannot be 0');
		}
		self::$IMG_WIDTH=(int)$w;
		self::$IMG_HEIGHT=(int)$h;
	}
	/**
	@return array with keys 'width' and 'height'.
	*/
	public static function getImagesSize(){
		return array('width'=>self::$IMG_WIDTH,'height'=>self::$IMG_HEIGHT);
	}
	/**
	@var int
	*/
	protected $id;
	/**
	@var string Service name
	*/
	protected $name;
	/**
	@var Components\Model\City
	*/
	protected $city;
	/**
	@var string
	*/
	protected $addr;
	/**
	@var DateInterval
	*/
	protected $duration;
	/**
	@var string
	*/
	protected $description;
	/**
	@var Components\Model\Type
	*/
	protected $type;
	/**
	@var Components\Strategy\AbstractPrice
	*/
	protected $price;
	/**
	@var Provider
	*/
	protected $provider;
	
	const STATUS_UNPUBLISHED=1;
	const STATUS_PUBLISHED=2;
	const STATUS_CHECKING=3;
	const STATUS_CHECK_FAILED=4;
	const STATUS_IN_BIN=5;
	/**
	@var int
	*/
	protected $status;
	/**
	@var int|null Maximal client count.
	*/
	protected $max_client_count;
	/**
	@var string
	*/
	protected $schedule;
	/**
	@var DoctrineCollection
	*/
	protected $requests;
	/**
	@var \DateTime
	*/
	protected $created_time;
	/**
	@var DoctrineCollection of Visitor.
	*/
	protected $wished_by;
	
	public function __construct(array $data=null){
		$this->requests=new DoctrineCollection();
		$this->whished_by=new DoctrineCollection();
		$this->status=self::STATUS_UNPUBLISHED;
		$this->max_client_count=NULL;
		$this->created_time=new \DateTime();
		parent::__construct($data);
	}
	/**
	@return int|null
	*/
	public function getId(){
		return $this->id;
	}
	/**
	@param string New name.
	*/
	public function setName($name){
		$this->name=(string)$name;
	}
	/**
	@return string
	*/
	public function getName(){
		return $this->name;
	}
	
	public function setCity(Components\Model\City $city){
		if($this->city){
			$this->city->removeService($this);
		}
		$this->city=$city;
		$city->addService($this);
	}
	/**
	@return Components\Model\City
	*/
	public function getCity(){
		return $this->city;
	}
	/**
	@param string
	*/
	public function setAddress($addr){
		$this->addr=(String)$addr;
	}
	/**
	@return string
	*/
	public function getAddress(){
		return $this->addr;
	}
	
	public function setDuration(DateInterval $dur){
		$this->duration=(int)$dur->format('%h');
	}
	/**
	@return DateInterval
	*/
	public function getDuration(){
		return new DateInterval('PT'.$this->duration.'H');
	}
	
	/**
	@param string
	*/
	public function setDescription($descr){
		$this->description=$descr;
	}
	/**
	@return string
	*/
	public function getDescription(){
		return $this->description;
	}
	/**
	@return string Short description(first 250 chars).
	*/
	public function getShortDescription(){
		if($descr=$this->getDescription()){
			return mb_substr($descr,0,500);
		}
		
		return NULL;
	}
	public function getReviews(){
		$revs=array();
		foreach($this->requests as $req){
			$rev=$req->getReview();
			if($rev&&$rev->isPublished()){
				$revs[]=$rev;
			}
			unset($rev);
		}
		return $revs;
	}
	
	public function setType(Components\Model\Type $type){
		$this->type=$type;
	}
	/**
	@return Components\Model\Type
	*/
	public function getType(){
		return $this->type;
	}
	
	public function setPrice(Components\PriceStrategy\Price $price){
		$this->price=$price;
	}
	/**
	Returns result of this.price.getCost.
	
	@param int Number of persons.	
	@return float
	*/
	public function getCost($num=1){
		return $this->price->getCost($num);
	}
	/**
	Returns result of this.price.getCurrency.
	
	@return Components\Currency
	*/
	public function getCurrency(){
		return $this->price->getCurrency();
	}
	/**
	@return Components\PriceStrategy\Price
	*/
	public function getPrice(){
		return $this->price;
	}
	
	public function setProvider(Provider $prov){
		$this->provider=$prov;
	}
	/**
	@return Provider
	*/
	public function getProvider(){
		return $this->provider;
	}
	/**
	@return int Number of timesthis service was successfully ordered.
	*/
	public function getPopularity(){
		$pop=0;
		foreach($this->requests as $req){
			if($req->isApproved()){
				++$pop;
			}
		}
		return $pop;
	}
	
	public function __toString(){
		return $this->getName();
	}
	
	public function unpublish(){
		$this->status=self::STATUS_UNPUBLISHED;
		foreach($this->requests as $key=>$req){
			if($req->isNew()){
				unset($this->requests[$key]);
			}
		}
	}
	
	public function publish(){
		$this->status=self::STATUS_PUBLISHED;
	}
	
	public function beingChecked(){
		$this->status=self::STATUS_CHECKING;
	}
	
	public function deny(){
		$this->status=self::STATUS_CHECK_FAILED;
	}
	
	public function isUnpublished(){
		return $this->status==self::STATUS_UNPUBLISHED;
	}
	
	public function isPublished(){
		return $this->status==self::STATUS_PUBLISHED;
	}
	
	public function isBeingChecked(){
		return $this->status==self::STATUS_CHECKING;
	}
	
	public function isDenied(){
		return $this->status==self::STATUS_CHECK_FAILED;
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setMaxClientCount($max){
		if($max<0){
			$max=0;
		}
		$this->max_client_count=(int)$max;
	}
	
	public function getMaxClientCount(){
		return $this->max_client_count;
	}
	/**
	@param string
	*/
	public function setSchedule($schedule){
		$this->schedule=(string)$schedule;
	}
	/**
	@return string
	*/
	public function getSchedule(){
		return $this->schedule;
	}
	
	public function getBookingRequests(){
		return $this->requests->toArray();
	}
	
	public function getApprovedBookingRequests(){
		$brs=$this->requests->toArray();
		foreach($brs as $key=>$br){
			if(!$br->isApproved()){
				unset($brs[$key]);
			}
		}
		return array_values($brs);
	}
	/**
	@param string Path ot img.
	
	@return bool On success.
	*/
	public function addImage($file){
		$id=1;
		$file=(string)$file;
		if(count($this->getImages())){
			$id=max(array_keys($this->getImages()))+1;
		}
		
		$filename=getcwd()."/public/img/service/img_".(($this->getId())? $this->getId().'_':'')."$id.";
		$info=getimagesize($file);
		$type=$info[2];
		$size=self::getImagesSize();
		$img_uploaded=NULL; //Uploaded img handler
		if($info[0] < $size['width'] && $info[1] < $size['height']){
			return FALSE;
		}
		switch($type){
		case IMAGETYPE_PNG:
			$filename.='png';
			$img_uploaded=imagecreatefrompng($file);
			break;
		case IMAGETYPE_JPEG:
			$filename.='jpg';
			$img_uploaded=imagecreatefromjpeg($file);
			break;
		default:
			return FALSE;
			break;
		}
		if(file_exists($filename)){
			imagedestroy($img_uploaded);
			return FALSE;
		}
		$img=imagecreatetruecolor($size['width'],$size['height']);
		$uploaded_width=$size['width']/$size['height']*$info[1];
		$uploaded_height=$info[1];
		if($uploaded_width > $info[0]){
			$uploaded_width=$info[0];
			$uploaded_height=$height/$size['width']*$info[0];
		}
		imagecopyresampled($img,$img_uploaded,0,0,($info[0]-$uploaded_width)/2,($info[1]-$uploaded_height)/2,$size['width'],$size['height'],$uploaded_width,$uploaded_height);
		imagejpeg($img,$filename);
		imagedestroy($img_uploaded);
		imagedestroy($img);
		unlink($file);
		return TRUE;
	}
	
	public function getImages(){
		$dir=getcwd().'/public/img/service';
		if(is_dir($dir)){
			$imgs=array();
			$files=scandir($dir);
			foreach($files as $file){
				if(preg_match('#^img_'.(($this->getId())? $this->getId().'_':'').'([0-9]+)\.([A-z0-9]+)$#',$file,$found)){
					if(in_array($found[2],array('png','jpg'))){
						$imgs[$found[1]]='/img/service/'.$found[0];
					}
				}
			}
			
			return $imgs;
		}
		else{
			throw new \Exception("$dir does not exists, cant get service images!");
		}
	}
	
	public function getImage($id){
		$imgs=$this->getImages();
		if(isset($imgs[$id])){
			return $imgs[$id];
		}
		return NULL;
	}
	
	public function removeImage($id){
		if($img=$this->getImage($id)){
			unlink(getcwd().'/public'.$img);
			return TRUE;
		}
		return FALSE;
	}
	
	public function getMainImage(){
		$imgs=$this->getImages();
		if(!$imgs)
			return NULL;
		$key=min(array_keys($imgs));
		return $imgs[$key];
	}
	
	public function setMainImage($id){
		$imgs=$this->getImages();
		if(!isset($imgs[$id])){
			return FALSE;
		}
		
		$old_main_img=$this->getMainImage();
		$old_main_img=array('id'=>array_search($old_main_img,$this->getImages()),'name'=>$old_main_img);
		$new_main_img=array('id'=>$id,'name'=>$imgs[$id]);
		if($new_main_img['id']!=$old_main_img['id']){
			rename(getcwd().'/public'.$old_main_img['name'],getcwd().'/public'.$old_main_img['name'].'.temp');
			rename(getcwd().'/public'.$new_main_img['name'],getcwd().'/public'.preg_replace('#[0-9]\.#',$old_main_img['id'].'.',$new_main_img['name']));
			rename(getcwd().'/public'.$old_main_img['name'].'.temp',getcwd().'/public'.preg_replace('#[0-9]\.#',$new_main_img['id'].'.',$old_main_img['name']));
		}
		return TRUE;
	}
	/**
	@return \DateTime
	*/
	public function getCreatedTime(){
		return $this->created_time;
	}
	/**
	@return float
	*/
	public function getAverageRating(){
		$rcount=0;
		$rsum=0;
		$revs=$this->getReviews();
		foreach($revs as $rev){
			if($rev->getRating()!==NULL){
				++$rcount;
				$rsum+=$rev->getRating();
			}
		}
		
		if(!$rcount){
			return NULL;
		}
		return $rsum/$rcount;
	}
	/**
	@param Visitor Add visitor who wished this.
	*/
	public function addWishedBy(Visitor $usr){
		if(!$this->isWishedBy($usr)){
			$this->wished_by[]=$usr;
		}
	}
	/**
	@param Visitor Is this in visitor's wishlist.
	@return bool
	*/
	public function isWishedBy(Visitor $usr){
		foreach($this->wished_by as $vis){
			if($vis->getId()==$usr->getId()){
				return TRUE;
			}
		}
		return FALSE;
	}
	/**
	Count of Visitors wished this.
	@return int
	*/
	public function wishedByCount(){
		return $this->wished_by->count();
	}
	/**
	@return array of Visitors wished this.
	*/
	public function getWishedBy(){
		return $this->wished_by->toArray();
	}
	/**
	@param Visitor
	*/
	public function removeWishedBy(Visitor $usr){
		foreach($this->wished_by as $key=>$visitor){
			if($visitor->getId()==$usr->getId()){
				unset($this->wished_by[$key]);
				return;
			}
		}
	}
	/**
	Marks this as removed to bin.
	*/
	public function setInBin($bool){
		if($bool){
			$this->status=self::STATUS_IN_BIN;
		}
		else{
			$this->status=self::STATUS_UNPUBLISHED;
		}
	}
	/**
	Was service moved to bin?.
	
	@return bool
	*/
	public function isInBin(){
		return $this->status==self::STATUS_IN_BIN;
	}
}
?>
