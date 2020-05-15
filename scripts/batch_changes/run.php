<?php
$included = get_included_files();
if(count($included)>1)die();

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

ob_start();

$CMSChange=function(&$CMS){
    $updaterPath=__DIR__.'/'.$CMS->CMSkey.'/updater.php';
    $phpver=$CMS::PHP_VERSION?:'7.1';
    $r=shell_exec('/usr/local/php-cgi/'.$phpver.'/bin/php '.$updaterPath.' "path='.$CMS->getPath().'"');
    echo $r."\n";
};

include_once(dirname(dirname(__DIR__)).'/classes/cms.iterator.class.php');
$iterator = new CMSIterator(dirname(dirname(dirname(__DIR__))));
$iterator->apply($CMSChange);

$result=ob_get_contents();
ob_end_clean();

echo $result;

include_once(dirname(dirname(__DIR__)).'/classes/reporter.class.php');
$reporter=new Reporter();
$pathinfo=explode('/',__DIR__);
$accaunt=$pathinfo[3];
if($accaunt!='effect43')$reporter->save($result,false);
$reporter->send($result);