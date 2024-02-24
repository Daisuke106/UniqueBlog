<?php
session_start(); // セッションを開始
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$message = ''; // アラートメッセージ

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['email'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // パスワードリセットのための確認コード生成関数
    function generateRandomCode($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    // データベースへの接続とエラーハンドリング
    try {
        $dbh = new PDO($dsn, $db_user, $db_password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // メールアドレスの重複チェック
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM finalusers WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $email_exists = ($stmt->fetchColumn() > 0);

        if ($email_exists) {
            // メールアドレスが存在する場合、ユーザー情報を取得
            $stmt = $dbh->prepare("SELECT app_password FROM finalusers WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                $app_password = $user_data['app_password'];

                // 生成した仮パスワード（確認コード）
                $confirmation_code = generateRandomCode();

                // メールを送信
                if (sendConfirmationEmail($email, $confirmation_code, $username, $app_password)) {
                    // メールが送信成功したら、仮パスワードをデータベースに保存
                    $stmt = $dbh->prepare("UPDATE finalusers SET confirmation_code = :confirmation_code WHERE email = :email");
                    $stmt->bindParam(':confirmation_code', $confirmation_code);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();

                    // セッションに確認コードを保存
                    $_SESSION['confirmation_code'] = $confirmation_code;

                    $message = "仮パスワードをメールで送信しました。Gmailを確認してください。";
                } else {
                    $message = "メールの送信に失敗しました。";
                }
            } else {
                $message = "ユーザー情報の取得に失敗しました。";
            }
        } else {
            $message = "ご登録がありません。";
        }
    } catch (PDOException $e) {
        $message = "データベースエラー: " . $e->getMessage();
    }
}

// 確認メールを送信する関数
function sendConfirmationEmail($email, $confirmation_code, $username, $app_password)
{
    // PHPMailerの設定とメール送信の処理を実装する

    // 以下はダミーの実装例です。SMTPサーバーの設定や認証情報、送信元メールアドレスなどを正しく設定してください。
    mb_language('uni');
    mb_internal_encoding('UTF-8');

    $mail = new PHPMailer(true);

    try {
        $host = 'smtp.gmail.com';
        $mailname = $email;
        $idname = $username;
        $password = $app_password; // AppPasswordを使用

        $from = 'k022c2145@g.neec.ac.jp';
        $fromname = 'Daistyle';

        $to = $email;
        $toname = $username;

        $subject = '仮パスワードの送付について';
        $body = "$idname 様\n"
            . "\n"
            . "\n"
            . "Daistyleへのご利用ありがとうございます。" . "\n"
            . "\n"
            . '仮パスワードのコード: ' . $confirmation_code
            . "\n"
            . "\n"
            . 'このコードを以下のサイトに入力してください。'
            . "\n"
            . 'http://localhost:3000/Login&Register_form/new_password.php'
            . "\n"
            . 'このメールに心当たりがない場合は、このメールを破棄してください。'
            . "\n"
            . 'Daistyle'
            . "\n"
            . 'http://localhost:3000/shop_site/top_page/main.php'
            . "\n"
            . "ポータルページからもアクセスできます。"
            . "http://localhost:3000/portal_page/portal_site.php";

        // デバッグ設定
        //$mail->SMTPDebug = 2; // デバッグ出力を有効化（レベルを指定）
        // $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str<br>";};

        // SMTPサーバの設定
        $mail->isSMTP(); // SMTPの使用宣言
        $mail->Host = $host; // SMTPサーバーを指定
        $mail->SMTPAuth = true; // SMTP authenticationを有効化
        $mail->Username = $mailname; // SMTPサーバーのユーザ名
        $mail->Password = $password; // SMTPサーバーのパスワード
        $mail->SMTPSecure = 'tls'; // 暗号化を有効（tls or ssl）無効の場合はfalse
        $mail->Port = 587; // TCPポートを指定（tlsの場合は465や587）
        $mail->setFrom($from, $fromname);
        $mail->addAddress($to, $toname);
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";

        // 送受信先設定（第二引数は省略可）
        /*
        $mail->setFrom('from@example.com', '差出人名'); // 送信者
        $mail->addAddress('to@xxxx.com', '受信者名');   // 宛先
        $mail->addReplyTo('replay@example.com', 'お問い合わせ'); // 返信先
        $mail->addCC('cc@example.com', '受信者名'); // CC宛先
        $mail->Sender = 'return@example.com'; // Return-path
         */

        // 送信内容設定
        $mail->Subject = $subject;
        $mail->Body = $body;

        // 送信
        return $mail->send();
    } catch (Exception $e) {
        // エラーの場合
        echo "メールの送信に失敗しました。: {$mail->ErrorInfo}";
        return false;
    }
}
?>




<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="../js/forgot_password.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/forgot.css">
    </head>
    <body>
    <div class="bg-img">
    <h2 style="margin-top: 0; text-align: center;">Forgot Password</h2>
    <form action="forgot_password.php" method="POST" style="margin: 0 auto; max-width: 400px;">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required placeholder="Enter your username" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email address" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">

        <button type="submit" style="box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); transition: 0.3s;">Submit</button>
    </form>
    <div id="message"><?php echo $message; ?></div>
    </div>
    </body>
</html>
