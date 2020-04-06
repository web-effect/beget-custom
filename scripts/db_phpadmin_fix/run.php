<?php
//Обновление паролей в phpmyadmin
//Для запуска в команду необходимо передать логин и пароль аккаунта
$included = get_included_files();
if(count($included)>1)die();

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 1);
ini_set('ignore_repeated_errors', 0);
ini_set('ignore_repeated_source', 0);
ini_set('report_memleaks', 1);
ini_set('track_errors', 1);
ini_set('docref_root', 0);
ini_set('docref_ext', 0);
ini_set('error_reporting', -1);
ini_set('log_errors_max_len', 0);

$params=array('login'=>$argv[1],'pwd'=>$argv[2]);
if(empty($params['login'])||empty($params['pwd']))die('Для работы необxодимо указать логин и пароль аккаунта');

function sendAppRequest($app,$params=array()){
    $service_url = 'https://api.beget.com/api/';
	$request = curl_init();
	curl_setopt_array($request,array(
		CURLOPT_URL=>$service_url.$app,
		CURLOPT_RETURNTRANSFER=>true,
		CURLOPT_SSL_VERIFYPEER=>false,
		CURLOPT_POST=>true,
		CURLOPT_POSTFIELDS=>http_build_query($params)
	));
	$response = curl_exec($request);
	curl_close($request);
	return json_decode($response,true)?:false;
}

$CMSUpdatePhpMyAdmin = function(&$CMS,$params){
    $config = $CMS->getConfig();
    if(!$config){
        echo $CMS->getErrors()."\n";
        return;
    }
    
    $input_data=array(
        'suffix'=>str_replace($params['login'].'_','',$config['db_user'])
        ,'access'=>$config['db_host']
        ,'password'=>$config['db_pwd']
    );
    $app_params=array(
        'login'=>$params['login']
        ,'passwd'=>$params['pwd']
        ,'output_format'=>'json'
        ,'input_format'=>'json'
        ,'input_data'=>json_encode($input_data)
    );
    $result = sendAppRequest('mysql/changeAccessPassword',$app_params);
    if(!$result||$result['status']!='success'){
        echo "Ошибка запроса к API\n";
    }elseif($result['answer']['status']!='success'){
        foreach($result['answer']['errors'] as $err){
            echo "[".$err['error_code']."]".$err['error_text']."\n";
        }
    }else{
        echo "Пароль обновлён\n";
    }
    sleep(3);
};

include_once(__DIR__.'/cms.iterator.class.php');
$iterator = new CMSIterator(dirname(__DIR__));
$iterator->apply($CMSUpdatePhpMyAdmin,$params);
