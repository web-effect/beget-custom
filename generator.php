<?php
$included = get_included_files();
if(count($included)>1)die();

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

function generate($chars_sets,$length){
    $str='';
    $sets=array(
        'al'=>"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
        ,'lower'=>"abcdefghijklmnopqrstuvwxyz"
        ,'upper'=>"ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        ,'num'=>"0123456789"
        ,'spec'=>"!@#$%^&*()_-=+;:,.?'`~[]{}|:;<>/"
    );
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


echo '$database_password = '.generate('al,num,spec',8)."\n";
echo '$table_prefix = '.generate('al,num',6).'_'."\n";
echo '$site_id = modx'.generate('lower,num',14).'.'.generate('num',8)."\n";
echo '$site_sessionname = SN'.generate('lower,num',13)."\n";
echo '$uuid = '.generate('lower,num',8).'-'.generate('lower,num',4).'-'.generate('lower,num',4).'-'.generate('lower,num',4).'-'.generate('lower,num',12)."\n";
echo '$modx_connectors_url = /backend/connectors_'.generate('al,num',12).'/'."\n";
