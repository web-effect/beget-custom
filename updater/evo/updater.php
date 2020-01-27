<?php
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
if($database_type == 'mysql'){
    $db_connected = false;
    echo 'НЕ удалось подключится к базе: используется расширение mysql'."\n";
}else{
    try {
        $modx->db->connect();
    } catch (Exception $e) {
        $db_connected = false;
        echo 'НЕ удалось подключится к базе: ',  $e->getMessage(), "\n";
    }
}




echo '1. Обновляем файлы'."\n";
echo '-- Копируем manager'."\n";
$managerfrom=__DIR__.'/files/manager';
$managerto=$_REQUEST['path']."/public_html/manager";
echo '---- из'.$managerfrom."\n";
echo '---- в'.$managerto."\n";
$cmd="mkdir -p ".$managerto.";  cp -r -a -v ".$managerfrom."/* ".$managerto." 2>&1 ";
echo '---- cmd '.$cmd."\n";
$r=shell_exec($cmd);
echo $r."\n";


echo '2. Обновляем пользователей'."\n";
if($db_connected){
    $modx->loadExtension('phpass');
    $delete_users=array(
        'info@effect-16.ru','info@effect78.ru','nk@web-kirov.ru','info@effect12.ru','446850@web-kirov.ru'
    );
    $change_users=array(
        'info@web-kirov.ru'=>array('password'=>'19effectus'),
        'goosevkir@gmail.com'=>array('password'=>'goosevbackend2019'),
        'tonnedo@gmail.com'=>array('password'=>'xaiQuah4'),
    );
    
    
    foreach($delete_users as $delete_user){
        echo '-- Удаление пользователя '.$delete_user."\n";
        $delete_user_id=$modx->db->getValue($modx->db->select('id', $modx->getFullTableName('manager_users'), "username='{$delete_user}'"));
        if(!$delete_user_id)$delete_user_id=$modx->db->getValue($modx->db->select('internalKey', $modx->getFullTableName('user_attributes'), "email='{$delete_user}'"));
        if(!$delete_user_id){
            echo '---- Пользователь не найден'."\n";
            continue;
        }
        
        $modx->db->delete($modx->getFullTableName('manager_users'), "id='{$delete_user_id}'");
        $modx->db->delete($modx->getFullTableName('member_groups'), "member='{$delete_user_id}'");
        $modx->db->delete($modx->getFullTableName('user_settings'), "user='{$delete_user_id}'");
        $modx->db->delete($modx->getFullTableName('user_attributes'), "internalKey='{$delete_user_id}'");
    }
    
    foreach($change_users as $change_user=>$fields){
        echo '-- Изменение пользователя '.$change_user."\n";
        $change_user_id=$modx->db->getValue($modx->db->select('id', $modx->getFullTableName('manager_users'), "username='{$change_user}'"));
        if(!$change_user_id)$change_user_id=$modx->db->getValue($modx->db->select('internalKey', $modx->getFullTableName('user_attributes'), "email='{$change_user}'"));
        if(!$change_user_id){
            echo '---- Пользователь не найден'."\n";
            continue;
        }
        if(isset($fields['password']))$fields['password']=$modx->phpass->HashPassword($fields['password']);
        $modx->db->update($fields, $modx->getFullTableName('manager_users'), "id='{$change_user_id}'");
    }
}else{
    echo '-- База не подключена'."\n";
}

echo 'Скрипт завершён успешно'."\n";
