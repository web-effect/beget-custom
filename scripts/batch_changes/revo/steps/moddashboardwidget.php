<?php

echo $idx.'. Обновляем виджеты'."\n";
foreach($config['modDashboardWidget'] as $ik=>$item){
    echo '-- Виджет '.($item['data']['name'])."\n";
    $spwo=$modx->getObject('modDashboardWidget',$item['criteria'],false);
    if(!$spwo){
        echo '-- Виджет НЕ найден, создать'."\n";
        $spwo=$modx->newObject('modDashboardWidget');
        $spwo->fromArray($item['data']);
        foreach($item['relates'] as $relate=>$objects){
            $relate=explode(':',$relate);
            if(is_numeric(current(array_keys($objects)))){
                $_objects=[];
                foreach($objects as $object){
                    $_object=$modx->newObject($relate[0]);
                    foreach($object as $k=>$v)$_object->set($k,$v);
                    $_objects[]=$_object;
                }
                $spwo->addMany($_objects,$relate[1]);
            }else{
                $_object=$modx->newObject($relate[0]);
                foreach($objects as $k=>$v)$_object->set($k,$v);
                $spwo->addOne($_object,$relate[1]);
            }
        }
    }
    $spwo->set('content',$item['data']['content']);
    if($spwo->save(false)){
        echo '-- Виджет обновлен'."\n";
    }else{
        echo '-- Виджет НЕ обновлен'."\n";
    }
}
