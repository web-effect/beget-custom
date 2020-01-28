<?php

$config=array(
    'charlists'=>array(
        'al'=>"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
        ,'lower'=>"abcdefghijklmnopqrstuvwxyz"
        ,'upper'=>"ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        ,'num'=>"0123456789"
        ,'spec'=>"!@#$%^&*()_-=+;:,.?'`~[]{}|:;<>/"
    ),
    'templates'=>[
        '$database_password = [[al,num,spec|8]]',
        '$table_prefix = [[al,num|6]]_',
        '$site_id = modx[[lower,num|14]].[[num|8]]',
        '$site_sessionname = SN[[lower,num|13]]',
        '$uuid = [[lower,num|8]]-[[lower,num|4]]-[[lower,num|4]]-[[lower,num|4]]-[[lower,num|12]]',
        '$modx_connectors_url = /backend/connectors_[[al,num|12]]/'
    ]
);