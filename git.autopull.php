<?php
$included = get_included_files();
if(count($included)>1)die();

$gitpath='~/.custom';

//Запрашиваем изменения
shell_exec('cd '.$gitpath.';git fetch 2>&1');
sleep(10);

//Запрашиваем статус
$status=shell_exec('cd '.$gitpath.';git status');
$changed=preg_match('#Your branch is behind#ui',$status);
if(!$changed){
    echo 'Изменений не найдено';
    die();
}

//Обновляем репозиторий
shell_exec('cd '.$gitpath.';git pull');

include(__DIR__.'/git.autopull.after.php');