<?php
function t($host,$user,$pass){
  try{
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=mini_ecommerce;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    echo "OK host=$host user=$user pass=".($pass===''?'(empty)':'(set)')."\n";
  }catch(Throwable $e){
    echo "FAIL host=$host user=$user pass=".($pass===''?'(empty)':'(set)')." => ".$e->getMessage()."\n";
  }
}
t('127.0.0.1','root','');
t('localhost','root','');
