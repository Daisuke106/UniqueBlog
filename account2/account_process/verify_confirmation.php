<?php
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = json_decode(file_get_contents('php://input'), true);
    $confirmation_code = $input_data['confirmation_code'] ?? '';

    if ($confirmation_code !== '') {
        try {
            $dbh = new PDO($dsn, $db_user, $db_password);

            $stmt_check_code = $dbh->prepare("SELECT COUNT(*) FROM finalusers WHERE confirmation_code = :confirmation_code");
            $stmt_check_code->bindParam(':confirmation_code', $confirmation_code);
            $stmt_check_code->execute();
            $code_exists = ($stmt_check_code->fetchColumn() > 0);

            if ($code_exists) {
                // 確認コードが正しい場合、is_confirmedカラムを更新する
                $stmt_update_confirmed = $dbh->prepare("UPDATE finalusers SET is_confirmed = 1 WHERE confirmation_code = :confirmation_code");
                $stmt_update_confirmed->bindParam(':confirmation_code', $confirmation_code);
                if ($stmt_update_confirmed->execute()) {
                    echo "success";
                } else {
                    echo "データベースの更新に失敗しました。";
                }
            } else {
                echo "確認コードが正しくありません。";
            }

            $dbh = null;
        } catch (PDOException $e) {
            echo "データベースに接続できませんでした。";
        }
    } else {
        echo "確認コードを入力してください。";
    }
} else {
    echo "無効なリクエストです。";
}
?>
