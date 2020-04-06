<?php

echo $idx.'. Обновляем пункты меню'."\n";

foreach($config['modMenu'] as $ik=>$item){
    echo '-- '.($item['data']['text']?:$ik)."\n";
    if($item['required']){
        foreach($item['required'] as $classname=>$objects){
            foreach($objects as $criteria){
                $object=$modx->getObject($classname,$criteria,false);
                if(!$object){
                    echo '---- Требования для изменения не выполнены'."\n";
                    continue 3;
                }
            }
        }
    }
    
    $pmo=$modx->getObject('modMenu',$item['criteria'],false);
    if(!$pmo){
        echo '---- Пункт меню НЕ найден, необходимо добавить в ручную'."\n";
    }else{
        echo '---- Пункт меню найден'."\n";
        if($item['data']['parent'])$pmo->set('parent',$item['data']['parent']);
        if($item['data']['menuindex'])$pmo->set('menuindex',$item['data']['menuindex']);
        $result = false;
        if($item['data']['text']){
            if($pmo->text!==$item['data']['text']){
                $npmo = $modx->getObject('modMenu',['text'=>$item['data']['text']],false);
                if(!$npmo)$npmo = $modx->newObject('modMenu',$pmo->toArray());
                $npmo->fromArray($pmo->toArray());
                $npmo->set('text',$item['data']['text']);
                $result = $npmo->save(false);
                if($result)$pmo->remove();
            }else{
                $result = $pmo->save(false);
            }
        }else{
            $result = $pmo->save(false);
        }
        
        if($result){
            echo '---- Пункт меню обновлен'."\n";
        }else{
            echo '---- Пункт меню НЕ обновлен'."\n";
        }
    }
}
