<?php
/*
для ранних версий evo? неоходимо использовать
$internalKey = $modx->db->insert($field, $tbl_manager_users);
$field['password'] = $modx->manager->genHash($newpassword, $internalKey);
$modx->db->update($field, $tbl_manager_users, "id='{$internalKey}'");
*/

echo $idx.'. Обновляем пользователей'."\n";
if($db_connected){
    $modx->loadExtension('phpass');

    foreach(array_unique($config['users']['remove']) as $delete_user){
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
    
    foreach($config['users']['update'] as $change_user=>$userdata){
        echo '-- Изменение пользователя '.$change_user."\n";
        $change_user_id=$modx->db->getValue($modx->db->select('id', $modx->getFullTableName('manager_users'), "username='{$change_user}'"));
        if(!$change_user_id)$change_user_id=$modx->db->getValue($modx->db->select('internalKey', $modx->getFullTableName('user_attributes'), "email='{$change_user}'"));
        if(!$change_user_id){
            echo '---- Пользователь не найден'."\n";
            continue;
        }
        $fields=$userdata;
        if(isset($fields['password'])){
            if($modx->phpass)$fields['password']=$modx->phpass->HashPassword($fields['password']);
            elseif(method_exists($modx->manager,'genHash'))$fields['password'] = $modx->manager->genHash($fields['password'], $change_user_id);
            else{
                echo '---- Изменение пароля невозможно. Раcширение phpass не загружено, genHash не доступен'."\n";
                continue;
            }
        }
        unset($fields['profile']);
        unset($fields['groups']);
        $modx->db->update($fields, $modx->getFullTableName('manager_users'), "id='{$change_user_id}'");
        
        if($userdata['profile']){
            $profile=$userdata['profile'];
            $profile = $modx->db->escape($profile);
            $modx->db->update($profile, $modx->getFullTableName('user_attributes'), "internalKey='{$change_user_id}'");
        }
		
    }
    
    foreach($config['users']['create'] as $username=>$userdata){
        echo '-- Создание пользователя '.$username."\n";
        $exist_user_id=$modx->db->getValue($modx->db->select('id', $modx->getFullTableName('manager_users'), "username='{$username}'"));
        if(!$exist_user_id)$exist_user_id=$modx->db->getValue($modx->db->select('internalKey', $modx->getFullTableName('user_attributes'), "email='{$username}'"));
        if($exist_user_id){
            echo '---- Пользователь уже существует'."\n";
            continue;
        }
        
        $fields=$userdata;
        if(!isset($fields['password'])){
            echo '---- Пароль не указан'."\n";
            continue;
        }
        unset($fields['profile']);
        unset($fields['groups']);
        $fields['username']=$username;
        
        if($modx->phpass){
            $fields['password']=$modx->phpass->HashPassword($fields['password']);
            $internalKey = $modx->db->insert($fields, $modx->getFullTableName('manager_users'));
        }
        elseif(method_exists($modx->manager,'genHash')){
            $internalKey = $modx->db->insert($fields, $modx->getFullTableName('manager_users'));
            $fields['password'] = $modx->manager->genHash($fields['password'], $internalKey);
            $modx->db->update($fields, $modx->getFullTableName('manager_users'), "id='{$internalKey}'");
        }
        else{
            echo '---- Создание пользователя невозможно. Раcширение phpass не загружено, genHash не доступен'."\n";
            continue;
        }
        
        
        $profile=$userdata['profile'];
        $profile['internalKey']=$internalKey;
		$profile = $modx->db->escape($profile);
		$modx->db->insert($profile, $modx->getFullTableName('user_attributes'));
		
		foreach($userdata['groups'] as $groupID){
		    $modx->db->insert(array('user_group'=>$groupID,'member'=>$internalKey), $modx->getFullTableName('member_groups'));
		}
    }

}else{
    echo '-- База не подключена'."\n";
}