<?php

echo $idx.'. Обновляем файлы'."\n";
$dataDir = dirname(__DIR__).'/data';

echo '-- Копируем core'."\n";
$corefrom=$dataDir.'/files/core';
$coreto=MODX_CORE_PATH;
echo '---- из'.$corefrom."\n";
echo '---- в'.$coreto."\n";
if($cache->copyTree($corefrom,$coreto)){
    echo '---- скопировано'."\n";
}else{
    echo '---- НЕ скопировано'."\n";
}

echo '-- Копируем manager'."\n";
$managerfrom=$dataDir.'/files/manager';
$managerto=MODX_MANAGER_PATH;
echo '---- из'.$managerfrom."\n";
echo '---- в'.$managerto."\n";
if($cache->copyTree($managerfrom,$managerto)){
    echo '---- скопировано'."\n";
}else{
    echo '---- НЕ скопировано'."\n";
}

echo '-- Копируем connectors'."\n";
$confrom=$dataDir.'/files/connectors';
$conto=MODX_CONNECTORS_PATH;
echo '---- из'.$confrom."\n";
echo '---- в'.$conto."\n";
if($cache->copyTree($confrom,$conto)){
    echo '---- скопировано'."\n";
}else{
    echo '---- НЕ скопировано'."\n";
}