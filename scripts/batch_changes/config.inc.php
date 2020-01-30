<?php

$config=array();
$accaunt_config=dirname(dirname(__DIR__)).'/config.inc.php';
var_dump($accaunt_config);
if(file_exists($accaunt_config))include_once($accaunt_config);

$config=array_merge_recursive(array(
    'update'=>[
        //'info@web-kirov.ru'=>array(),
    ],
    'users'=>[
        'remove'=>[
            'nk@web-kirov.ru','nk@web-kirov.ru'
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
