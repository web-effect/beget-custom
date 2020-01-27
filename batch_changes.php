<?php
$included = get_included_files();
if(count($included)>1)die();

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

$CMSChange=function(&$CMS){
    if($CMS::CMS=='MODx Revolution'){
        $r=shell_exec('/usr/local/php-cgi/7.1/bin/php ~/.custom/updater/revo/updater.php "path='.$CMS->getPath().'"');
        echo $r."\n";
        
    }
    if($CMS::CMS=='MODx Evolution'){
        $r=shell_exec('/usr/local/php-cgi/7.1/bin/php ~/.custom/updater/evo/updater.php "path='.$CMS->getPath().'"');
        echo $r."\n";
        
    }
    
};

include_once(__DIR__.'/classes/cms.iterator.class.php');
$iterator = new CMSIterator(dirname(__DIR__));
$iterator->apply($CMSChange);
