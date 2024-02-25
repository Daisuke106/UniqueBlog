<?php

// データベース接続
$db = new PDO('mysql:host=localhost;dbname=nin_nin_board;charset=utf8mb4', 'root', 'password');

// 接続成功の場合
if ($db) {
  echo 'success';
} else {
  echo 'failed';
}

?>
