<?php

echo $idx.'. Обновляем пользователей'."\n";

if(!empty($config['users']['remove'])){
    $dq=$modx->newQuery('modUser',null,false);
    $dq->leftJoin('modUserProfile','profile','modUser.id=profile.internalKey');
    $dq->where(array('modUser.username:IN'=>array_unique($config['users']['remove']),'OR:profile.email:IN'=>array_unique($config['users']['remove'])));
    $dq->prepare();
    $delete_users_it=$modx->getIterator('modUser',$dq,false);
    foreach($delete_users_it as $delete_user){
        echo '-- Удаление пользователя '.$delete_user->username."\n";
        $delete_user->remove();
    }
}

if(!empty($config['users']['update'])){
    $cq=$modx->newQuery('modUser',null,false);
    $cq->leftJoin('modUserProfile','profile','modUser.id=profile.internalKey');
    $cq->where(array('modUser.username:IN'=>array_keys($config['users']['update']),'OR:profile.email:IN'=>array_keys($config['users']['update'])));
    $cq->select(array($modx->getSelectColumns('modUser','modUser'),'profile.email as profile_email'));
    $cq->prepare();
    $change_users_it=$modx->getIterator('modUser',$cq,false);
    foreach($change_users_it as $change_user){
        echo '-- Изменение пользователя '.$change_user->username."\n";
        $new_data=$config['users']['update'][$change_user->username]?:$config['users']['update'][$change_user->profile_email];
        if(!$new_data){
            echo '---- Не найдены данные для '.$change_user->username."\n";
            continue;
        }
        $change_user->fromArray($new_data);
        $change_user->save(false);
    }
}

if(!empty($config['users']['create'])){
    $crq=$modx->newQuery('modUser',null,false);
    $crq->leftJoin('modUserProfile','profile','modUser.id=profile.internalKey');
    $crq->where(array('modUser.username:IN'=>array_keys($config['users']['create']),'OR:profile.email:IN'=>array_keys($config['users']['create'])));
    $crq->select(array('modUser.username as user__name','profile.email as user__email'));
    $crq->prepare();
    $crq->stmt->execute();
    $created_users=$crq->stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $created_users=array_unique(array_merge(array_values($created_users),array_keys($created_users)));
    foreach($config['users']['create'] as $username=>$userdata){
        echo '-- Создание пользователя '.$username."\n";
        if(in_array($username,$created_users)){
            echo '---- Пользователь уже существует'."\n";
            continue;
        }
        
        $user = $modx->newObject('modUser',$userdata);
        $user->set('username', $username);
        $user->set('password', $userdata['password']);
        $user->setSudo($userdata['sudo']?:0);
        $user->save();
        
        $profile = $modx->newObject('modUserProfile',$userdata['profile']);
        $user->addOne($profile);
        $profile->save();
        $user->save();
        
        foreach($userdata['groups'] as $groupName=>$groupRole){
          $group = $modx->getObject('modUserGroup', array('name' => $groupName));
          if(!$group)continue;
          $groupMember = $modx->newObject('modUserGroupMember');
          $groupMember->set('user_group', $group->get('id'));
          $groupMember->set('role', $groupRole);
          $groups[] = $groupMember;
        }
        
        $user->addMany($groups);
        $user->save();
    }
}


//Удалить сессии
$sessionTable = $modx->getTableName('modSession');
if ($modx->query("TRUNCATE TABLE {$sessionTable}") == false) {
    echo '-- Сессии НЕ удалены '."\n";
} else {
    echo '-- Сессии удалены '."\n";
}