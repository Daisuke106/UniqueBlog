<?php
// create_finalusers_table.php
// データベースにfinalusersテーブルを作成する

// データベースに接続する設定
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root'; // データベースユーザ名
$db_password = ''; // データベースパスワード

// データベースに接続
try {
    $connection = new PDO($dsn, $db_user, $db_password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit();
}

// テーブルが既に存在するかチェックするクエリ
$checkQuery = "SHOW TABLES LIKE 'finalusers'";
$result = $connection->query($checkQuery);

if ($result->rowCount() > 0) {
    echo "既にテーブルが存在します。";
} else {
    // テーブル作成のクエリを設定
    $createQuery = "CREATE TABLE finalusers (
        id INT(10) PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        email VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        password VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        app_password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        failed_attempts INT(11) DEFAULT 0,
        time DATETIME NOT NULL,
        confirmation_code VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
        is_confirmed TINYINT(1) DEFAULT 0,
        lockout_time DATETIME DEFAULT NULL,
        total_failed_attempts INT(11) DEFAULT 0,
        login_count INT(11)
    )";

    // クエリの実行
    if ($connection->exec($createQuery)) {
        echo "テーブルが作成されました。";
    } else {
        echo "テーブルの作成に失敗しました。";
    }
}

// データベース接続を閉じる
$connection = null;
?>
