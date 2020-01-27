<?php
include_once(dirname(__DIR__).'/_base.class.php');


class EVO_Controller extends BASE_Controller{
    const CMS='MODx Evolution';
    public $CMSkey='evo';
    protected $path = '';
    protected $errors = array();
    private $config_file_path = false;
    
    public function __construct($path){
        parent::__construct($path);
    }
    
    static function getFromPath($path){
        $is=file_exists($path.'/public_html/manager/includes/config.inc.php')&&is_file($path.'/public_html/manager/includes/config.inc.php');
        if(!$is)return false;
        return parent::getFromPath($path);
    }
    
    private function getConfigFilePath(){
        if(empty($this->config_file_path)){
            $this->config_file_path = $this->path.'/public_html/manager/includes/config.inc.php';
        }
        if(!file_exists($this->config_file_path)||!is_file($this->config_file_path)){
            $this->config_file_path = false;
            $this->errors[]='Файл конфигурации не найден';
        }
        return $this->config_file_path;
    }
    
    public function getConfig(){
        $config_file_path = $this->getConfigFilePath();
        if(!$config_file_path)return false;
        
        $config_file = file_get_contents($config_file_path);
        $config=array();
        
        preg_match_all('/\$database_(\w*?)\s*?=\s*?\'(.*?)\'/m',$config_file,$db_config,PREG_SET_ORDER);
        foreach($db_config as $db_config_row){
            switch($db_config_row[1]){
                case 'type': $config['db_type']=$db_config_row[2];break;
                case 'user': $config['db_user']=$db_config_row[2];break;
                case 'server': $config['db_host']=$db_config_row[2];break;
                case 'password': $config['db_pwd']=$db_config_row[2];break;
            }
        }
        return !empty($config)?$config:false;
    }
    
    public function setConfig($config){
        $config_file_path = $this->getConfigFilePath();
        if(!$config_file_path)return false;
        
        $old_config_file = file_get_contents($config_file_path);
        $config_file = $old_config_file;
        
        foreach($config as $key=>$value){
            $value = str_replace("'","\'",$value);
            $value = str_replace('\\','\\\\',$value);
            $value = str_replace('$','\$',$value);
            switch($key){
                case 'db_type': $config_file=preg_replace('/(\$database_type\s*?=\s*?)\'.*?\'/m','$1\''.$value."'",$config_file);break;
                case 'db_user': $config_file=preg_replace('/(\$database_user\s*?=\s*?)\'.*?\'/m','$1\''.$value."'",$config_file);break;
                case 'db_host': $config_file=preg_replace('/(\$database_server\s*?=\s*?)\'.*?\'/m','$1\''.$value."'",$config_file);break;
                case 'db_pwd': $config_file=preg_replace('/(\$database_password\s*?=\s*?)\'.*?\'/m','$1\''.$value."'",$config_file);break;
            }
        }
        
        $result = file_put_contents($config_file_path,$config_file);
        if(!$result){
            $result=false;
            file_put_contents($config_file_path,$old_config_file);
        }
        
        return $result;
    }
}


return 'EVO_Controller';