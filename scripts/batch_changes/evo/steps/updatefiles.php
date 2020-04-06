<?php

echo $idx.'. Обновляем файлы'."\n";
echo '-- Копируем manager'."\n";
$dataDir = dirname(__DIR__).'/data';

$managerfrom=$dataDir.'/files/manager';
$managerto=$_REQUEST['path']."/public_html/manager";
echo '---- из'.$managerfrom."\n";
echo '---- в'.$managerto."\n";
$cmd="mkdir -p ".$managerto.";  cp -r -a -v ".$managerfrom."/* ".$managerto." 2>&1 ";
echo '---- cmd '.$cmd."\n";
$r=shell_exec($cmd);
echo $r."\n";