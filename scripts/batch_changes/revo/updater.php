<?php
/*ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 1);
ini_set('ignore_repeated_errors', 0);
ini_set('ignore_repeated_source', 0);
ini_set('report_memleaks', 1);
ini_set('track_errors', 1);
ini_set('docref_root', 0);
ini_set('docref_ext', 0);
ini_set('error_reporting', -1);
ini_set('log_errors_max_len', 0);*/

$included = get_included_files();
if(count($included)>1)die();

parse_str($argv[1],$_REQUEST);
$_SERVER['DOCUMENT_ROOT']=$_REQUEST['path']."/public_html";

require $_REQUEST['path']."/public_html/config.core.php";
if(!defined('MODX_CORE_PATH')) require_once '../../../config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

if(!$modx->connect()){
    echo 'Не удалось подключится к базе данных '."\n";
    exit();
}

$pathinfo=explode('/',$_REQUEST['path']);
$accaunt=$pathinfo[3];
$site=$pathinfo[4];
include_once(__DIR__.'/config.inc.php');
//var_dump($site,$config);
if(in_array($site,$config['exclude'])){
    echo 'Сайт находится в исключениях, пропускаем'."\n";
    exit();
}

$modx->loadClass('modCacheManager', '', true, false);
$cache = new modCacheManager($modx);
$idx=1;
foreach($config['steps'] as $step){
    $file=__DIR__.'/steps/'.preg_replace('#[^\w]+#','_',strtolower($step)).'.php';
    if(file_exists($file)){
        $vars = array_keys(get_defined_vars());
        include_once($file);
        $vars = array_diff(array_keys(get_defined_vars()),$vars);
        foreach($vars as $varkey){
            unset(${$varkey});
        }
    }else{
        echo 'Шаг '.$step.' пропущен'."\n";
    }
    $idx++;
}

echo 'Чистим кэш'."\n";
$cache->deleteTree(MODX_CORE_PATH.'cache/',array('deleteTop'=>true,'extensions'=>array()));
echo 'Скрипт завершён успешно'."\n";