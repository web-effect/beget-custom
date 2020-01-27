<?php

$included = get_included_files();
if(strpos(__DIR__,dirname($included[0]))!==0)die();

class CMSIterator{
    private $root='';
    private $classmap=array();
    
    public function __construct($root){
        $this->root=$root;
    }
    
    public function loadClasses(){
        $dir=__DIR__.'/cms/';
        $handler = opendir(__DIR__.'/cms/');
        while (($path = readdir($handler)) !== FALSE){
            if ($path != '.' && $path != '..' && is_dir($dir.'/'.$path)){
                $CMSClass = include_once($dir.'/'.$path.'/cms.class.php');
                if(!class_exists($CMSClass)){
                    echo "Не удалось загрузить класс работы с CMS ".$CMSClass."\n";
                    echo "-------------------------------------------\n\n";
                    continue;
                }
                $this->classmap[$path]=$CMSClass;
            }
        }
    }
    
    public function apply(callable $callback,$params=array()){
        if(empty($this->root))return false;
        
        $this->loadClasses();
        
        $root_handler = opendir($this->root);
        while (($path = readdir($root_handler)) !== FALSE){
            /*if(!preg_match('/33-imperial-otel/i',$path))continue;*/
            if ($path != '.' && $path != '..' && is_dir($this->root.'/'.$path)){
                $path=$this->root.'/'.$path;
                echo "-------------------------------------------\n";
                echo 'Проверяем путь: '.$path."\n";
                $is_site = file_exists($path.'/public_html')&&is_dir($path.'/public_html');
                if(!$is_site){
                    echo "Это не сайт\n";
                    echo "-------------------------------------------\n\n";
                    continue;
                }
                
                $CMS=false;
                foreach($this->classmap as $CMSkey=>$className){
                    $CMS=$className::getFromPath($path);
                    if($CMS){
                        if(!$CMS->CMSkey)$CMS->CMSkey=$CMSkey;
                        echo "Сайт под управлением ".$CMS::CMS."\n";
                        break;
                    }
                }
                if(!$CMS){
                    echo "Не удалось определить систему управления сайтом\n";
                    echo "-------------------------------------------\n\n";
                    continue;
                }
                $result=$callback($CMS,$params);

                echo "-------------------------------------------\n\n";
           }
        }
        closedir($root_handler);
        
    }
    
    
}