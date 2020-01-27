<?php
$included = get_included_files();
if(count($included)>1)die();

parse_str($argv[1],$_REQUEST);

$_SERVER['DOCUMENT_ROOT']=$_REQUEST['path']."/public_html";

require $_REQUEST['path']."/public_html/config.core.php";
if(!defined('MODX_CORE_PATH')) require_once '../../../config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

echo '1. Прикрепляем TV image к главной'."\n";
$imgtv=$modx->getObject('modTemplateVar',array('name'=>'image'),false);
if(!$imgtv){
    echo '-- TV image НЕ найден'."\n";
}
else{
    echo '-- TV image найден, ID='.$imgtv->id."\n";
    $mainResID=(int)$modx->getOption('site_start',null,1);
     echo '-- ID главной страницы = '.$mainResID."\n";
    $mainRes=$modx->getObject('modResource',$mainResID,false);
    if($mainRes){
        echo '-- Главная страница найдена, TEMPLATE='.$mainRes->template."\n";
        $tvt=$modx->getObject('modTemplateVarTemplate',array('tmplvarid'=>$imgtv->id,'templateid'=>$mainRes->template),false);
        if(!$tvt){
            echo '-- Привязка image к главной требуется'."\n";
            $tvt=$modx->newObject('modTemplateVarTemplate');
            $tvt->set('tmplvarid',$imgtv->id);
            $tvt->set('templateid',$mainRes->template);
            $tvt->set('rank',0);
            if($tvt->save(false)){
                echo '-- Привязка image к главной создана'."\n";
            }else{
                echo '-- Привязка image к главной НЕ создана'."\n";
            }
        }else{
            echo '-- Привязка image к главной НЕ требуется'."\n";
        }
    }else{
        echo '-- Главная страница НЕ найдена'."\n";
    }
}


echo '2. Обновляем файлы'."\n";
$modx->loadClass('modCacheManager', '', true, false);
$cache = new modCacheManager($modx);

echo '-- Копируем core'."\n";
$corefrom=__DIR__.'/files/core';
$coreto=MODX_CORE_PATH;
echo '---- из'.$corefrom."\n";
echo '---- в'.$coreto."\n";
if($cache->copyTree($corefrom,$coreto)){
    echo '---- скопировано'."\n";
}else{
    echo '---- НЕ скопировано'."\n";
}

echo '-- Копируем manager'."\n";
$managerfrom=__DIR__.'/files/manager';
$managerto=MODX_MANAGER_PATH;
echo '---- из'.$managerfrom."\n";
echo '---- в'.$managerto."\n";
if($cache->copyTree($managerfrom,$managerto)){
    echo '---- скопировано'."\n";
}else{
    echo '---- НЕ скопировано'."\n";
}

echo '-- Копируем connectors'."\n";
$confrom=__DIR__.'/files/connectors';
$conto=MODX_CONNECTORS_PATH;
echo '---- из'.$confrom."\n";
echo '---- в'.$conto."\n";
if($cache->copyTree($confrom,$conto)){
    echo '---- скопировано'."\n";
}else{
    echo '---- НЕ скопировано'."\n";
}


echo '3. Обновляем словарь'."\n";
echo '-- core login ru login_forget_your_login'."\n";
$lo_arr=array('namespace'=>'core','topic'=>'login','language'=>'ru','name'=>'login_forget_your_login');
$lo=$modx->getObject('modLexiconEntry',$lo_arr,false);
if(!$lo){
    echo '---- Запись словаря НЕ найдена, создать'."\n";
    $lo=$modx->newObject('modLexiconEntry',$lo_arr);
}else{
    echo '---- Запись словаря найдена'."\n";
}
$lo->set('value','Забыли пароль?');
if($lo->save(false)){
    echo '---- Запись словаря обновлена'."\n";
}else{
    echo '---- Запись словаря НЕ обновлена'."\n";
}



echo '4. Обновляем настройки'."\n";
echo '-- support_url'."\n";
$so_arr=array('key'=>'support_url');
$so=$modx->getObject('modSystemSetting',$so_arr,false);
if(!$so){
    echo '---- Настройка НЕ найдена, создать'."\n";
    $so=$modx->newObject('modSystemSetting');
    $so->set('key',$so_arr['key']);
}else{
    echo '---- Настройка найдена'."\n";
}
$so->set('namespace','core');
$so->set('area','manager');
$so->set('value','//web-kirov.ru/abvgd');

if($so->save(false)){
    echo '---- Настройка обновлена'."\n";
}else{
    echo '---- Настройка НЕ обновлена'."\n";
}



echo '5. Обновляем пункты меню'."\n";
echo '-- перейти на сайт'."\n";
$pmo=$modx->getObject('modMenu',array('text'=>'preview','OR:handler:='=>'MODx.preview(); return false;'),false);
if(!$pmo){
    echo '---- Пункт меню НЕ найден, необходимо добавить в ручную'."\n";
}else{
    echo '---- Пункт меню найден'."\n";
    $pmo->set('parent','topnav');
    $pmo->set('menuindex',99);
    $result = false;
    if($pmo->text!=='На сайт'){
        $npmo = $modx->newObject('modMenu',$pmo->toArray());
        $npmo->set('text','На сайт');
        $result = $npmo->save(false);
        if($result)$pmo->remove();
    }else{
        $result = $pmo->save(false);
    }

    if($result){
        echo '---- Пункт меню обновлен'."\n";
    }else{
        echo '---- Пункт меню НЕ обновлен'."\n";
    }
}
echo '-- Заказы shopkeeper'."\n";
$shk = $modx->getObject('modSnippet',array('name'=>'Shopkeeper3'));
if($shk&&is_dir(MODX_CORE_PATH.'components/shopkeeper3/')){
    echo '---- Shopkeeper установлен'."\n";
    $pmo=$modx->getObject('modMenu',array('namespace'=>'shopkeeper3','action'=>'index'),false);
    if(!$pmo){
        echo '------ Пункт меню НЕ найден, необходимо добавить в ручную'."\n";
    }else{
        echo '------ Пункт меню найден'."\n";
        $pmo->set('parent','topnav');
        $pmo->set('menuindex',98);
        $pmo->set('description','');
        $result = false;
        if($pmo->text!=='Заказы'){
            $npmo = $modx->newObject('modMenu',$pmo->toArray());
            $npmo->set('text','Заказы');
            $result = $npmo->save(false);
            if($result)$pmo->remove();
        }else{
            $result = $pmo->save(false);
        }
        
        if($result){
            echo '------ Пункт меню обновлен'."\n";
        }else{
            echo '------ Пункт меню НЕ обновлен'."\n";
        }
    }
}else{
    echo '---- Shopkeeper НЕ установлен'."\n";
}


echo '6. Обновляем виджет поддержки'."\n";
$spwo=$modx->getObject('modDashboardWidget',array('name:LIKE'=>'%Техподдержка для сайта%'),false);
if(!$spwo){
    echo '-- Виджет НЕ найден, создать'."\n";
    $spwo=$modx->newObject('modDashboardWidget');
    $spwo->fromArray(array(
        'name'=>'Техподдержка для сайта [[++site_name]]',
        'type'=>'html',
        'namespace'=>'core',
        'lexicon'=>'core:dashboards',
        'size'=>'double'
    ));
    $spwpo=$modx->newObject('modDashboardWidgetPlacement');
    $spwpo->set('dashboard',1);
    $spwpo->set('rank',0);
    $placements=array($spwpo);
    $spwo->addMany($placements,'Placements');
}

$spwo->set('content',"<iframe align='center' height='555px' width='900px' src='[[++support_url]]' style='margin:0px auto 0px auto;border:none;display:block;'></iframe>");
if($spwo->save(false)){
    echo '-- Виджет обновлен'."\n";
}else{
    echo '-- Виджет НЕ обновлен'."\n";
}


echo '7. Обновляем CSS'."\n";
$CSS=MODX_BASE_PATH.'assets/components/extra/mgr/css/extra.css';
if(!file_exists($CSS)){
    echo '-- Extra css НЕ найден используем index.css'."\n";
    $CSS=MODX_MANAGER_PATH.'templates/default/css/index.css';
}
$content = file_get_contents($CSS);
$matches=array();
preg_match('/\.dashboard-block-double\s+\.body{([^}]*?)}/uis',$content,$matches);
if(!$matches[0]){
    echo '-- Правило НЕ найдно, добавляем'."\n";
    $content.="\n\n.dashboard-block-double .body{\n\tmax-height: none;\n}";
}elseif(!preg_match('/max-height:\s*?none/uis',$matches[0])){
    echo '-- Правило найдно, свойство НЕ найдено, добавляем'."\n";
    $repl=str_replace($matches[1],$matches[1]."\n\tmax-height: none;\n",$matches[0]);
    $content = str_replace($matches[0],$repl,$content);
}else{
    echo '-- Правило найдно, свойство найдено'."\n";
}
file_put_contents($CSS,$content);


echo '8. Обновляем Extra если есть'."\n";
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


echo '8. Обновляем пользователей'."\n";
    $delete_users=array(
        'info@effect-16.ru','info@effect78.ru','nk@web-kirov.ru','info@effect12.ru','446850@web-kirov.ru'
    );
    $change_users=array(
        'info@web-kirov.ru'=>array('password'=>'19effectus'),
        'goosevkir@gmail.com'=>array('password'=>'goosevbackend2019'),
        'tonnedo@gmail.com'=>array('password'=>'xaiQuah4'),
    );

    $dq=$modx->newQuery('modUser',null,false);
    $dq->leftJoin('modUserProfile','profile','modUser.id=profile.internalKey');
    $dq->where(array('modUser.username:IN'=>$delete_users,'OR:profile.email:IN'=>$delete_users));
    $dq->prepare();
    $delete_users_it=$modx->getIterator('modUser',$dq,false);
    foreach($delete_users_it as $delete_user){
        echo '-- Удаление пользователя '.$delete_user->username."\n";
        $delete_user->remove();
    }

    $cq=$modx->newQuery('modUser',null,false);
    $cq->leftJoin('modUserProfile','profile','modUser.id=profile.internalKey');
    $cq->where(array('modUser.username:IN'=>array_keys($change_users),'OR:profile.email:IN'=>array_keys($change_users)));
    $cq->select(array($modx->getSelectColumns('modUser','modUser'),'profile.email as profile_email'));
    $cq->prepare();
    $change_users_it=$modx->getIterator('modUser',$cq,false);
    foreach($change_users_it as $change_user){
        echo '-- Изменение пользователя '.$change_user->username."\n";
        $new_data=$change_users[$change_user->username]?:$change_users[$change_user->profile_email];
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




echo 'Чистим кэш'."\n";
$cache->deleteTree(MODX_CORE_PATH.'cache/',array('deleteTop'=>true,'extensions'=>array()));
echo 'Скрипт завершён успешно'."\n";