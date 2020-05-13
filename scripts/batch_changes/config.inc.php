<?php

$config=array();
$accaunt_config=__DIR__.'/config.acc.inc.php';
if(file_exists($accaunt_config))include_once($accaunt_config);

$config=array_merge_recursive(array(
    'exclude'=>['18-arm-lab.ru'],
    'users'=>[
        'update'=>[
            //'info@web-kirov.ru'=>array(),
        ],
        'remove'=>[
            'nk@web-kirov.ru','nk@web-kirov.ru','206866@mail.ru',
            'adeks220@mail.ru'
        ],
        'create'=>[
            /*'tonnedo@gmail.com'=>[
                'password'=>'**********',
                'profile'=>[
                    'email'=>'tonnedo@gmail.com'
                ]
            ]*/
        ]
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
