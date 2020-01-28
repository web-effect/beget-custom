<?php

echo $idx.'. Обновляем Extra если есть'."\n";
$extra = $modx->getObject('modPlugin',array('name'=>'Extra'));
if(!$extra){
    echo '-- Плагин НЕ найден'."\n";
}elseif(!is_dir(MODX_CORE_PATH.'components/extra/')){
    echo '-- Компонент НЕ используется. обновление не требуется'."\n";
}else{
    $content = $extra->plugincode;
    $useswitch = preg_match('/switch\s*\(\s*\$modx->event->name\s*\)/uis',$content);
    $useif = preg_match('/if\s*\(\$modx->event->name\s*==/uis',$content);
    
    if($useif){
        echo '-- В плагине используются if'."\n";
        $isset = preg_match('/if\s*\(\$modx->event->name\s*==\s*[\'"]OnManagerPageBeforeRender[\'"]\s*\){[^}]*?\$modx->extra->loadMgrFiles[^}]*?}/uis',$content);
    }
    elseif($useswitch){
        echo '-- В плагине используются case'."\n";
        $isset = preg_match('/case\s+[\'"]OnManagerPageBeforeRender[\'"]\s*:\s*{[^}]*?\$modx->extra->loadMgrFiles[^}]*?}/uis',$content);
    }
    if($isset)echo '-- Код для OnManagerPageBeforeRender уже добавлен'."\n";
    
    $event = $modx->getObject('modPluginEvent',array('pluginid'=>$extra->id,'event'=>'OnManagerPageBeforeRender'));
    if($event)echo '-- Событие OnManagerPageBeforeRender уже добавлено'."\n";

    if(!($useif||$useswitch)){
        echo '-- Не удалось обновить плагин, не найден if или case'."\n";
    }elseif(!$isset&&!$event){
            $content = str_replace('$modx->extra->loadMgrFiles','//$modx->extra->loadMgrFiles',$content);
            $matched = false;
            
            if($useif){
                $matches=array();
                preg_match('/if\s*\(\$modx->event->name\s*==/uis',$content,$matches,PREG_OFFSET_CAPTURE);
                if($matches[0]){
                    $before=substr($content,0,$matches[0][1]);
                    $after=substr($content,$matches[0][1]);
                    $content=$before."
if(\$modx->event->name == 'OnManagerPageBeforeRender'){
    \$modx->controller->addLexiconTopic('extra:default');
    \$modx->extra->loadMgrFiles();
}
"
                    .$after;
                    $matched=true;
                }else{
                    echo '-- Не удалось обновить плагин, не найден if'."\n";
                }
            }elseif($useswitch){
                $matches=array();
                preg_match('/switch\s*\(\s*\$modx->event->name\s*\)\s*{/uis',$content,$matches,PREG_OFFSET_CAPTURE);
                if($matches[0]){
                    $before=substr($content,0,$matches[0][1]+strlen($matches[0][0]));
                    $after=substr($content,$matches[0][1]+strlen($matches[0][0]));
                    $content=$before."
        case 'OnManagerPageBeforeRender':{
        	\$modx->controller->addLexiconTopic('extra:default');
        	\$modx->extra->loadMgrFiles();
        	break;
        }
    "
                    .$after;
                    $matched=true;
                }else{
                    echo '-- Не удалось обновить плагин, не найден switch'."\n";
                }
            }
            
            if($matched){
                $event=$modx->newObject('modPluginEvent');
                $event->set('pluginid',$extra->id);
                $event->set('event','OnManagerPageBeforeRender');
                if(!$event->save(false)){
                    echo '-- Не удалось обновить плагин, событие OnManagerPageBeforeRender НЕ добавлено'."\n";
                }else{
                    echo '-- Событие OnManagerPageBeforeRender добавлено'."\n";
                    $extra->set('plugincode',$content);
                    if(!$extra->save(false)){
                        echo '-- НЕ удалось обновить плагин, ошибка при сохранении'."\n";
                        $event->remove();
                    }else{
                        echo '-- Плагин обновлён'."\n";
                    }
                }
                
            }
        
    }else{
        echo '-- Обновление плагина не требуется'."\n";
    }
}