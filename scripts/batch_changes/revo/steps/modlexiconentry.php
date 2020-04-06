<?php

echo $idx.'. Обновляем словарь'."\n";

foreach($config['modLexiconEntry'] as $data){
    $criteria=array_diff_key($data,array_flip(array('value')));
    echo '-- '.implode(' ',$criteria)."\n";
    $lo=$modx->getObject('modLexiconEntry',$criteria,false);
    if(!$lo){
        echo '---- Запись словаря НЕ найдена, создать'."\n";
        $lo=$modx->newObject('modLexiconEntry',$data);
    }else{
        echo '---- Запись словаря найдена'."\n";
    }
    $lo->set('value',$data['value']);
    if($lo->save(false)){
        echo '---- Запись словаря обновлена'."\n";
    }else{
        echo '---- Запись словаря НЕ обновлена'."\n";
    }
}