<?php
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root'; // データベースユーザ名
$db_password = ''; // データベースパスワード

try {
    $dbh = new PDO($dsn, $db_user, $db_password);
    $stmt = $dbh->prepare("SELECT * FROM finalusers");
    $stmt->execute();
    echo "success";
} catch (PDOException $e) {
    echo "error";
}
?>
