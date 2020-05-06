<?php

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
    
    foreach($config['users']['update'] as $change_user=>$fields){
        echo '-- Изменение пользователя '.$change_user."\n";
        $change_user_id=$modx->db->getValue($modx->db->select('id', $modx->getFullTableName('manager_users'), "username='{$change_user}'"));
        if(!$change_user_id)$change_user_id=$modx->db->getValue($modx->db->select('internalKey', $modx->getFullTableName('user_attributes'), "email='{$change_user}'"));
        if(!$change_user_id){
            echo '---- Пользователь не найден'."\n";
            continue;
        }
        if(!$modx->phpass&&isset($fields['password'])){
            echo '---- Изменение пароля не возможно. Раcширение phpass не загружено'."\n";
            continue;
        }
        if(isset($fields['password']))$fields['password']=$modx->phpass->HashPassword($fields['password']);
        $modx->db->update($fields, $modx->getFullTableName('manager_users'), "id='{$change_user_id}'");
    }
    
    if(!$modx->phpass){
        echo '-- Создание пользователей не возможно. Раcширение phpass не загружено'."\n";
    }else{
        foreach($config['users']['create'] as $username=>$userdata){
            echo '-- Создание пользователя '.$username."\n";
            $exist_user_id=$modx->db->getValue($modx->db->select('id', $modx->getFullTableName('manager_users'), "username='{$username}'"));
            if(!$exist_user_id)$exist_user_id=$modx->db->getValue($modx->db->select('internalKey', $modx->getFullTableName('user_attributes'), "email='{$username}'"));
            if($exist_user_id){
                echo '---- Пользователь уже существует'."\n";
                continue;
            }
            
            $fields=$userdata;
            if(isset($fields['password']))$fields['password']=$modx->phpass->HashPassword($fields['password']);
            $fields['username']=$username;
            unset($fields['profile']);
            unset($fields['groups']);
            $internalKey = $modx->db->insert($fields, $modx->getFullTableName('manager_users'));
            
            $profile=$userdata['profile'];
            $profile['internalKey']=$internalKey;
    		$profile = $modx->db->escape($profile);
    		$modx->db->insert($profile, $modx->getFullTableName('user_attributes'));
    		
    		foreach($userdata['groups'] as $groupID){
    		    $modx->db->insert(array('user_group'=>$groupID,'member'=>$internalKey), $modx->getFullTableName('member_groups'));
    		}
        }
    }
    
}else{
    echo '-- База не подключена'."\n";
}