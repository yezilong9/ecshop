<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include 'phpqrcode/phpqrcode.php'; //载入生成二维码必要文件


$value = isset($_GET['url'])?$_GET['url']:'';
$img_name = isset($_GET['goods_id'])?$_GET['goods_id']:'';
if(!$value || !$img_name) return false;
$obj = new codeImg($value,$img_name);
/**
* 生成商品二维码
* 2014-07-16
*
**/
class codeImg{

    #logo图片
    private $logo = 'codelogo.png';
    #开启添加logo
    public $start = true;
    #容错级别
    private $errorCorrectionLevel = 'Q';
    #图片大小
    public $matrixPointSize = 3;
    #logo图片宽度 
    private $logo_width = '';
    #logo图片高度  
    private $logo_height = '';
    #二维码存放路径
    private $path = 'images/code/';
    
    public function __construct($value,$img_name)
    {
		$this->matrixPointSize = isset($_GET['max'])?$_GET['max']:$this->matrixPointSize;
        if($value && $img_name)
        $this->create_code($value,$img_name);
    }
    //
    public function create_code($value,$img_name)
    {
        $value = $_GET['url']; //二维码内容 
        $QR =  $this->path.$img_name.'.png';
        if(!file_exists($QR))
        {
            //生成二维码图片 
            QRcode::png($value, $QR, $this->errorCorrectionLevel, $this->matrixPointSize, 2);
        }
        if($this->start) $QR = $this->logo_code($QR);
        Header("Content-type: image/png");  
        ImagePng($QR); 
    }
    
    public function logo_code($QR)
    {
        $logo = $this->logo;//logo图片 
        
        $QR = imagecreatefromstring(file_get_contents($QR)); 
        $logo = imagecreatefromstring(file_get_contents($logo)); 
        $QR_width = imagesx($QR);//二维码图片宽度 
        $QR_height = imagesy($QR);//二维码图片高度 
        $logo_width = $this->logo_width?$this->logo_width:imagesx($logo);//logo图片宽度 
        $logo_height = $this->logo_height?$this->logo_height:imagesy($logo);//logo图片高度 
        $logo_qr_width = $QR_width / 5; 
        $scale = $logo_width/$logo_qr_width; 
        $logo_qr_height = $logo_height/$scale; 
        $from_width = ($QR_width - $logo_qr_width) / 2; 
        //重新组合图片并调整大小 
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
        $logo_qr_height, $logo_width, $logo_height);
        return $QR;
    }
}
?>