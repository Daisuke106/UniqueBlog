<?php
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

// リクエストからスキャンされたIDを取得
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$scannedId = $data['id'];

try {
    $dbh = new PDO($dsn, $db_user, $db_password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ユーザーのUsernameとemailを取得
    $stmt = $dbh->prepare("SELECT Username, email FROM finalusers WHERE id = :id");
    $stmt->bindParam(':id', $scannedId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 認証成功
        $username = $user['Username'];
        $email = $user['email'];
        echo json_encode(['authenticated' => true, 'username' => $username, 'email' => $email]); // ユーザー名とメールアドレスを含める
    } else {
        // 認証失敗
        echo json_encode(['authenticated' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['authenticated' => false, 'error' => $e->getMessage()]);
}
?>
