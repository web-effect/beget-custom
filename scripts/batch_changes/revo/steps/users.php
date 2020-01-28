<?php

echo $idx.'. Обновляем пользователей'."\n";

$dq=$modx->newQuery('modUser',null,false);
$dq->leftJoin('modUserProfile','profile','modUser.id=profile.internalKey');
$dq->where(array('modUser.username:IN'=>$config['users']['remove'],'OR:profile.email:IN'=>$config['users']['remove']));
$dq->prepare();
$delete_users_it=$modx->getIterator('modUser',$dq,false);
foreach($delete_users_it as $delete_user){
    echo '-- Удаление пользователя '.$delete_user->username."\n";
    $delete_user->remove();
}

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



//Удалить сессии
$sessionTable = $modx->getTableName('modSession');
if ($modx->query("TRUNCATE TABLE {$sessionTable}") == false) {
    echo '-- Сессии НЕ удалены '."\n";
} else {
    echo '-- Сессии удалены '."\n";
}