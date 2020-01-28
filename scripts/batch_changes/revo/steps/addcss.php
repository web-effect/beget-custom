<?php

echo $idx.'. Обновляем CSS'."\n";
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