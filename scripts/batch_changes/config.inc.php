<?php

$config=array();
$accaunt_config=__DIR__.'/config.acc.inc.php';
if(file_exists($accaunt_config))include_once($accaunt_config);

$config=array_merge_recursive(array(
    'exclude'=>['18-arm-lab.ru'],
    'users'=>[
        /*'update'=>[
            'tonnedo@gmail.com'=>['password'=>'**********'],
            'alexey1mishin@mail.ru'=>['password'=>'**********']
        ],
        'create'=>[
            'tonnedo@gmail.com'=>[
                'password'=>'**********',
                'profile'=>[
                    'email'=>'tonnedo@gmail.com'
                ]
            ],
            'alexey1mishin@mail.ru'=>[
                'password'=>'**********',
                'profile'=>[
                    'email'=>'alexey1mishin@mail.ru'
                ]
            ]
        ],*/
        'remove'=>[
            
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
