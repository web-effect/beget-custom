<?php

$config=array();
$accaunt_config=__DIR__.'/config.acc.inc.php';
if(file_exists($accaunt_config))include_once($accaunt_config);

$config=array_merge_recursive(array(
    'users'=>[
        'update'=>[
            //'info@web-kirov.ru'=>array(),
        ],
        'remove'=>[
            'nk@web-kirov.ru','nk@web-kirov.ru','206866@mail.ru'
        ],
        
    ]
),$config?:array());

switch($accaunt){
    case 'effect43':{
        //Конфиг для effect43
        break;
    }
    case 'lpberiya':{
        //Конфиг для lpberiya
        break;
    }
}
