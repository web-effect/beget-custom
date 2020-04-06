<?php

echo $idx.'. Обновляем настройки'."\n";

foreach($config['modSystemSetting'] as $data){
    echo '-- '.$data['key']."\n";
    $criteria=array('key'=>$data['key']);
    $so=$modx->getObject('modSystemSetting',$criteria,false);
    if(!$so){
        echo '---- Настройка НЕ найдена, создать'."\n";
        $so=$modx->newObject('modSystemSetting');
    }else{
        echo '---- Настройка найдена'."\n";
    }
    foreach($data as $f=>$v){
        $so->set($f,$v);
    }

    if($so->save(false)){
        echo '---- Настройка обновлена'."\n";
    }else{
        echo '---- Настройка НЕ обновлена'."\n";
    }
}

