<?php
$included = get_included_files();
if(count($included)>1)die();

$gitpath='~/.custom';

//Запрашиваем изменения
$changes=shell_exec('cd '.$gitpath.';git fetch');
if(empty($changes)){
    echo 'Изменений не найдено';
    die();
}
//Обновляем репозиторий
shell_exec('cd '.$gitpath.';git pull');

include(__DIR__.'/git.autopull.after.php');