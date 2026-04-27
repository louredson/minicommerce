<?php
$tries = [
  ['root',''],
  ['root','root'],
  ['root','1234'],
  ['admin','admin'],
];
foreach ($tries as $t) {
  [$u,$p] = $t;
  try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mini_ecommerce;charset=utf8mb4',$u,$p,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $count = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    echo "OK user=$u pass=$p products=$count\n";
  } catch (Throwable $e) {
    echo "FAIL user=$u pass=$p => " . $e->getMessage() . "\n";
  }
}
