<?php
$included = get_included_files();
if(count($included)>1)die();

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

include_once(__DIR__.'/config.inc.php');

function generate($chars_sets,$length){
    global $config;
    $str='';
    $sets=$config['charlists'];
    if(!is_array($chars_sets))$chars_sets=explode(',',$chars_sets);
    $chars = implode('',array_intersect_key($sets,array_flip($chars_sets)));
    
    $i=0;
    $lenchars = strlen($chars);
    while($i<$length){
        $str.=$chars[random_int(0,$lenchars-1)];
        $i++;
    }
    return $str;
}



foreach($config['templates'] as $template){
    while(true){
        $matches=array();
        preg_match('#\[\[([^\]]*?)\]\]#ui',$template,$matches,PREG_OFFSET_CAPTURE);
        if(empty($matches))break;
        $replace=$matches[0][0];
        $offset=$matches[0][1];
        $params=explode('|',$matches[1][0]);
        $replacement=generate($params[0],$params[1]);
        $template = mb_substr($template,0,$offset).$replacement.mb_substr($template,$offset+mb_strlen($replace));
    }

    echo $template."\n";
}