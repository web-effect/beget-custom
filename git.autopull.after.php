<?php
$included = get_included_files();
if(strpos($included[0],__DIR__)!==0)die();

//Применить изменения
$r=shell_exec('/usr/local/php-cgi/7.1/bin/php ~/.custom/scripts/batch_changes/run.php');

//Сменить пароли
//r+=shell_exec('/usr/local/php-cgi/7.1/bin/php ~/.custom/scripts/db_pass_change/php.php');

echo $r;

//+