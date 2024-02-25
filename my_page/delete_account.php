<?php
session_start(); // セッションを開始

// ログイン済みか確認
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    // ログインしていない場合はログインページにリダイレクト
    header('Location: qr_login.php');
    exit;
}

// セッションからユーザー情報を取得
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];

// データベースに接続
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

try {
    $dbh = new PDO($dsn, $db_user, $db_password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ユーザーのアカウントを削除
    // $stmt = $dbh->prepare("DELETE FROM finalusersaddress WHERE user_id = :user_id");
    $stmt = $dbh->prepare("DELETE FROM finalusers WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // セッションを破棄してログアウト
    session_unset();
    session_destroy();

    // アカウント削除完了のJavaScriptアラートを表示
    echo '<script>alert("アカウントが削除されました。");</script>';
    // 指定のサイトにリダイレクト
    echo '<script>window.location.href = "http://localhost:3000/portal_page/portal_site.php";</script>';
} catch (PDOException $e) {
    // データベースエラーの処理
    echo '<script>alert("アカウント削除中にエラーが発生しました。");</script>';
    // エラーが発生した場合はマイページにリダイレクト
    echo '<script>window.location.href = "http://localhost:3000/my_page/customer_info.php";</script>';
}
?>
