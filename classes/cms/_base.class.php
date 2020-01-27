<?php
abstract class BASE_Controller{
    const CMS=null;
    public $CMSkey = '';
    protected $path = '';
    protected $errors = array();
    
    
    public function __construct($path){
        $this->path=$path;
    }
    
    static function getFromPath($path){
        $classname=static::class;
        return new $classname($path);
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function getConfig(){
        return false;
    }
    
    public function setConfig($config){
        return false;
    }
    
    public function getErrors(){
        return implode("\n",$this->errors);
    }
}