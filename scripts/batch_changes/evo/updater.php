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

require_once $_REQUEST['path']."/public_html/manager/includes/config.inc.php";
require_once $_REQUEST['path']."/public_html/manager/includes/document.parser.class.inc.php";
$modx = new DocumentParser;
$modx->loadExtension("ManagerAPI");
$modx->getSettings();
$etomite = &$modx; // for backward compatibility
$modx->tstart = $tstart;
$modx->mstart = $mstart;
$db_connected = true;
try {
    $modx->db->connect();
} catch (Exception $e) {
    $db_connected = false;
    echo 'НЕ удалось подключится к базе: ',  $e->getMessage(), "\n";
}

$pathinfo=explode('/',$_REQUEST['path']);
$accaunt=$pathinfo[3];
$site=$pathinfo[4];
include(__DIR__.'/config.inc.php');
//var_dump($site,$config);
if(in_array($site,$config['exclude'])){
    echo 'Сайт находится в исключениях, пропускаем'."\n";
    exit();
}

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

echo 'Скрипт завершён успешно'."\n";