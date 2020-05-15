<?php

class Reporter{
    public $reciver='https://effect-help.ru/assets/components/.custom/connector.php';
    
    public function __construct(){
        $this->public_key=openssl_pkey_get_public(file_get_contents(dirname(__DIR__).'/cert.pem'));
        $this->secret_key=openssl_pkey_get_private(file_get_contents(dirname(__DIR__).'/cert.priv.pem'))?:false;
    }
    
    public function send($message){
        $messages=[];
        $offset=0;
        while($offset<strlen($message)){
            $encrypted='';
            openssl_public_encrypt(substr($message,$offset,256), $encrypted, $this->public_key);
            $offset+=256;
            $messages[]=base64_encode($encrypted);
            while ($msg = openssl_error_string())
                echo "OpenSSL error when doing foo:" . $msg . "<br />\n";
        }

        $curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $this->reciver,
			CURLOPT_POSTFIELDS => http_build_query(array('message'=>$messages)),
		));
		
		$result = curl_exec($curl);
		curl_close($curl);
		
		//var_dump($result);
    }
    
    public function getReportFile(){
        $lastfile = false;
        $lastchange = 0;
        foreach (glob(dirname(__DIR__).'/reports' . '/*') as $f) {
            if(filemtime($f)>$lastchange){
                $lastchange=filemtime($f);
                $lastfile=$f;
            }
        }
        if(time()-$lastchange>300)$lastfile=dirname(__DIR__).'/reports/'.strftime('%Y-%m-%d__%H-%M');
        
        return $lastfile;
    }
    
    public function save($messages,$encrypted=true){
        if(!is_array($messages))$messages=array($messages);
        
        foreach($messages as &$message){
            if($encrypted){
                openssl_private_decrypt(base64_decode($message), $decrypted, $this->secret_key);
                $message=$decrypted;
            }
        }
        $message=implode('',$messages);
        
        $message="\n\n------------------------------------------------------------------------------\n"
        .strftime('%F %T')."\n".$message;
        
        $reportFile=$this->getReportFile();
        file_put_contents($reportFile,$message,FILE_APPEND);
    }
}