<?php

echo $idx.'. Прикрепляем TV к главной'."\n";
foreach($config['linkToMainTVs'] as $tvname){
    $tv=$modx->getObject('modTemplateVar',array('name'=>$tvname),false);
    if(!$tv){
        echo '-- TV '.$tvname.' НЕ найден'."\n";
    }
    else{
        echo '-- TV '.$tvname.' найден, ID='.$tv->id."\n";
        $mainResID=(int)$modx->getOption('site_start',null,1);
         echo '-- ID главной страницы = '.$mainResID."\n";
        $mainRes=$modx->getObject('modResource',$mainResID,false);
        if($mainRes){
            echo '-- Главная страница найдена, TEMPLATE='.$mainRes->template."\n";
            $tvt=$modx->getObject('modTemplateVarTemplate',array('tmplvarid'=>$tv->id,'templateid'=>$mainRes->template),false);
            if(!$tvt){
                echo '-- Привязка '.$tvname.' к главной требуется'."\n";
                $tvt=$modx->newObject('modTemplateVarTemplate');
                $tvt->set('tmplvarid',$tv->id);
                $tvt->set('templateid',$mainRes->template);
                $tvt->set('rank',0);
                if($tvt->save(false)){
                    echo '-- Привязка '.$tvname.' к главной создана'."\n";
                }else{
                    echo '-- Привязка '.$tvname.' к главной НЕ создана'."\n";
                }
            }else{
                echo '-- Привязка '.$tvname.' к главной НЕ требуется'."\n";
            }
        }else{
            echo '-- Главная страница НЕ найдена'."\n";
        }
    }
}


