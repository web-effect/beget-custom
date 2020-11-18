<?php
$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 4096,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

//Создать публичный и приватный ключ
$res = openssl_pkey_new($config);

// Извлекаем закрытый ключ из $res в $privKey
openssl_pkey_export($res, $privKey);
file_put_contents(__DIR__.'/cert.priv.pem',$privKey);

// Извлечение открытого ключа из $res в $pubKey
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];
file_put_contents(__DIR__.'/cert.pem',$pubKey);