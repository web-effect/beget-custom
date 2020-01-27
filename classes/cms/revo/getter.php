<?php
$included = get_included_files();
if(count($included)>1)die();

parse_str($argv[1],$_REQUEST);

switch($_REQUEST['action']){
    case 'config':{
        $_SERVER['DOCUMENT_ROOT']=$_REQUEST['path']."/public_html";
        
        require $_REQUEST['path']."/public_html/config.core.php";
        if(!defined('MODX_CORE_PATH')) require_once '../../../config.core.php';
        
        echo json_encode(array("MODX_CORE_PATH"=>MODX_CORE_PATH,"MODX_CONFIG_KEY"=>MODX_CONFIG_KEY));
    }
}