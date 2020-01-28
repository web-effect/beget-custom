<?php
include_once(dirname(__DIR__).'/config.inc.php');

$config=array_merge(array(
    'steps'=>[
        'linkTVsToMain',
        'updateFiles',
        'modLexiconEntry',
        'modSystemSetting',
        'modMenu',
        'modDashboardWidget',
        'addCSS',
        'updateExtra',
        'users',
    ],
    'linkToMainTVs'=>['image'],
    'modLexiconEntry'=>[
        ['namespace'=>'core','topic'=>'login','language'=>'ru','name'=>'login_forget_your_login','value'=>'Забыли пароль?'],
        ['namespace'=>'core','topic'=>'user','language'=>'ru','name'=>'user_created_password_message','value'=>'Пользователь создан.<br>Вход:&nbsp;[[+login_url]]<br>Логин: [[+username]]<br>Пароль: [[+password]]'],
        ['namespace'=>'core','topic'=>'user','language'=>'ru','name'=>'user_updated_password_message','value'=>'Пользователь обновлён.<br>Вход:&nbsp;[[+login_url]]<br>Логин: [[+username]]<br>Пароль: [[+password]]'],
    ],
    'modSystemSetting'=>[
        ['key'=>'support_url','namespace'=>'core','area'=>'manager','value'=>'//web-kirov.ru/abvgd']
    ],
    'modMenu'=>[
        [
            'criteria'=>['text'=>'preview','OR:handler:='=>'MODx.preview(); return false;'],
            'data'=>['parent'=>'topnav','menuindex'=>99,'text'=>'На сайт']
        ],[
            'required'=>['modNamespace'=>[['name'=>'shopkeeper3']]],
            'criteria'=>['namespace'=>'shopkeeper3','action'=>'index'],
            'data'=>['parent'=>'topnav','menuindex'=>98,'text'=>'Заказы']
        ]
    ],
    'modDashboardWidget'=>[
        [
            'criteria'=>['name:LIKE'=>'%Техподдержка для сайта%'],
            'data'=>[
                'name'=>'Техподдержка для сайта [[++site_name]]',
                'type'=>'html',
                'namespace'=>'core',
                'lexicon'=>'core:dashboards',
                'size'=>'double',
                'content'=>"<iframe align='center' height='555px' width='900px' src='[[++support_url]]' style='margin:0px auto 0px auto;border:none;display:block;'></iframe>"
            ],
            'relates'=>[
                'modDashboardWidgetPlacement:Placements'=>[
                    ['dashboard'=>1,'rank'=>0]
                ]
            ]
        ]
    ],
),$config);