<?php
// your_server_endpoint.php - サーバー側の処理

// POSTリクエストから顔データを受信
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->faceData)) {
    // 顔データをデータベースに保存する処理を実行
    // 以下は例として、MySQLデータベースへの保存処理です。

// データベース接続設定
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

    try {
        $dbh = new PDO($dsn, $db_user, $db_password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 顔データを保存するSQLクエリを実行
        $sql = "INSERT INTO face_users (face_data) VALUES (:faceData)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':faceData', $data->faceData);
        $stmt->execute();

        // 成功時のレスポンスを返す
        http_response_code(200);
        echo json_encode(['message' => '顔認証データが保存されました。']);
    } catch (PDOException $e) {
        // エラーメッセージを返す
        http_response_code(500);
        echo json_encode(['message' => 'データベースエラー: ' . $e->getMessage()]);
    }
} else {
    // エラーメッセージを返す
    http_response_code(400);
    echo json_encode(['message' => '顔データが空です。']);
}
?>
