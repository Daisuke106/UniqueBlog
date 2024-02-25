<?php
session_start(); // セッションを開始
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = ''; // アラートメッセージ

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmation_code']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $confirmation_code = $_POST['confirmation_code'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 6 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $message = "新しいパスワードは6文字以上で、英大文字と小文字、数字が含まれる必要があります。";
    } elseif ($new_password !== $confirm_password) {
        $message = "新しいパスワードと確認用のパスワードが一致しません。";
    } else {
        try {
            $dbh = new PDO($dsn, $db_user, $db_password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 確認コード（仮パスワード）が一致するかを確認
            $stmt = $dbh->prepare("SELECT email, username, app_password FROM finalusers WHERE confirmation_code = :confirmation_code");
            $stmt->bindParam(':confirmation_code', $confirmation_code);
            $stmt->execute();
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                // ハッシュ化された新しいパスワードを生成
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // パスワードを更新
                $stmt = $dbh->prepare("UPDATE finalusers SET password = :password, confirmation_code = NULL WHERE email = :email");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':email', $user_data['email']);
                $stmt->execute();

                // メールを送信
                if (sendPasswordChangeEmail($user_data['email'], $user_data['username'], $user_data['app_password'])) {
                    $message = "パスワードの変更が完了しました。";
                } else {
                    $message = "パスワードの変更が完了しましたが、メールの送信に失敗しました。";
                }
            } else {
                $message = "確認コードが無効です。";
            }
        } catch (PDOException $e) {
            $message = "データベースエラー: " . $e->getMessage();
        }
    }
}

// 確認メールを送信する関数
function sendPasswordChangeEmail($email, $username, $app_password) {
    // メール送信処理を実装する

    mb_language('uni');
    mb_internal_encoding('UTF-8');

    $mail = new PHPMailer(true);

    try {
        $host        = 'smtp.gmail.com';
        $mailname    = $email;
        $idname      = $username;
        $password    = $app_password;// AppPasswordを使用
    
        $from        = 'k022c2145@g.neec.ac.jp';
        $fromname    = 'にんにん版';
    
        $to = $email;
        $toname = $username;
    
        $subject = 'パスワードの変更が完了しました';
        $body =  "$idname 様\n"
                . "\n"
                . "\n"
                ."にんにん版へのご利用ありがとうございます。" . "\n"
                . "\n"
                ."パスワードの変更が完了しました。\n"
                ."パスワードの変更が行われていない場合は、お問い合わせください。" . "\n"
                . "\n"
                . '引き続き、にんにん版をお楽しみください。。'
                . "\n"
                . 'にんにん版'
                . "\n"
                . 'http://localhost:3000/shop_site/top_page/main.php'
                . "\n"
                . "ポータルページからもアクセスできます。"
                . "http://localhost:3000/portal_page/portal_site.php";
    
        // SMTPサーバの設定
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailname;
        $mail->Password   = $password;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from, $fromname);
        $mail->addAddress($to, $toname);
        $mail->CharSet = "UTF-8";
        $mail->Encoding="base64";
    
        // 送信内容設定
        $mail->Subject = $subject;
        $mail->Body    = $body;
    
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
    <link rel="icon" href="http://localhost:3000/logo/text.ico">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            text-align: center;
        }

        form {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>New Password</h2>
    <form action="new_password.php" method="POST">
        <label for="confirmation_code">Confirmation Code:</label>
        <input type="text" id="confirmation_code" name="confirmation_code" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Change</button>
    </form>
    <div id="message"><?php echo $message; ?></div>
    <script>
    // PHPからのメッセージを表示するためのJavaScriptコード
    const messageElement = document.getElementById('message');
    const messageText = messageElement.textContent.trim();

    if (messageText) {
        if (messageText.includes('パスワードの変更が完了しました。')) {
            alert(messageText);
            // リダイレクトを行う
            window.location.href = 'http://localhost:3000/shop_site/top_page/main.php';
        } else {
            alert(messageText);
        }
    }
</script>

</body>
</html>