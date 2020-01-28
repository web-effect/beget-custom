<?php
include_once(dirname(__DIR__).'/config.inc.php');

$config=array_merge(array(
    'steps'=>[
        'updateFiles',
        'users',
    ],
),$config);