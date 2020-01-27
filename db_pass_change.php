<?php
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

function generatePass(){
    $pass='';
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $i=0;
    $lenchars = strlen($chars);
    while($i<8){
        $pass.=$chars[random_int(0,$lenchars-1)];
        $i++;
    }
    return $pass;
}

function mysql_escape($string) {
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $string);
}

function changeDBPass($config,$oldpass,$newpass){
    $result=false;
    switch($config['db_type']){
        case 'mysqli':
        case 'mysql':{
            //var_dump($config);
            if(empty($config['db_user'])||empty($config['db_host'])){
                echo "Пользователь базы данных не указан\n";
                break;
            }
            if(empty($newpass)){
                echo "Новый пароль не указан\n";
                break;
            }
            $sql_command = "SET PASSWORD FOR '".$config['db_user']."'@'".$config['db_host']."' = '".mysql_escape($newpass)."'";
            $command = "mysql -u ".$config['db_user']." -h ".$config['db_host']." --password=".escapeshellarg($oldpass)." -e ".escapeshellarg($sql_command)." -v -v 2>&1";
            //var_dump($command);
            
            $result_code = false;
            ob_start();
            passthru($command,$result_code);
            $output = ob_get_contents();
            ob_end_clean();
            
            if($result_code===0 && strpos($output,"Query OK, 0 rows affected")!==false)return true;
            else{
                echo 'Не удалось изменить пароль - ['.$result_code.'] '.$output."\n";
            }

            break;
        }
        default:{
            echo 'Неизвестный тип базы - '.$config['db_type']."\n";
        }
    }

    return $result;
}

$CMSChangePass=function(&$CMS){
    $config = $CMS->getConfig();
    var_dump($config);
    if(!$config){
        echo $CMS->getErrors()."\n";
        return;
    }
    
    $newPass = generatePass();
    $oldPass = $config['db_pwd'];

    $pass_changed = changeDBPass($config,$oldPass,$newPass);
    
    if($pass_changed){
        $config['db_pwd']=$newPass;
        $pass_saved=$CMS->setConfig($config);
        if(!$pass_saved){
            echo "Не удалось сохранить конфигурацию\n";
            echo $CMS->getErrors()."\n";
            $pass_returned = changeDBPass($config,$newPass,$oldPass);
            echo $pass_returned?"Старый пароль восстановлен\n":"Не удалось восстановить старый пароль\n";
            return;
        }
        echo "Пароль успешно изменён\n";
    }
};

include_once(__DIR__.'/classes/cms.iterator.class.php');
$iterator = new CMSIterator(dirname(__DIR__));
$iterator->apply($CMSChangePass);
