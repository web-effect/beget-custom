<?php
include_once(dirname(__DIR__).'/config.inc.php');

$config=array_merge_recursive(array(
    'steps'=>[
        'updateFiles',
        'users',
    ],
    'users'=>[
        'create'=>[
            /*'tonnedo@gmail.com'=>[
                'profile'=>[
                    'role'=>1
                ],
            ]*/
        ]
    ]
),$config?:array());
