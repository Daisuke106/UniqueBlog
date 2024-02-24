<?php

// test_send_email.php

// 必要なライブラリやデータベース接続設定を読み込む
require '..\vendor\autoload.php'; // PHPMailerのautoload.phpへのパスを指定
require 'send_complete_email.php'; // send_complete_email.phpへのパスを指定

// データベース接続設定
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

try {
    $dbh = new PDO($dsn, $db_user, $db_password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // finalusersテーブルからテスト用のユーザーデータを取得
    $stmt = $dbh->prepare("SELECT email, username, app_password, id FROM finalusers");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        foreach ($users as $user) {
            $email = $user['email'];
            $username = $user['username'];
            $app_password = $user['app_password'];
            $id = $user['id'];

            // テスト用にメール送信を呼び出す
            sendCompleteEmailResponse($email, $username, $app_password, $id);

            echo "success";
        }
    } else {
        echo "テスト用のユーザーデータが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <title>Send Email Test</title>
    <style>
    </style>
</head>
<body>
    <h1>メール送信テスト</h1>
    <form action="test_send_email.php" method="post">
        <button type="submit">メール送信テスト実行</button>
    </form>
    <script>
    </script>
</body>
</html>
