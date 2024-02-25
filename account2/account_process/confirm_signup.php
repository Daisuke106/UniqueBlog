<?php
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

require '..\vendor\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmationCode = $_POST['confirmationCode']; // フロントエンドから送られた確認コード

    try {
        $dbh = new PDO($dsn, $db_user, $db_password);

        // データベースから該当の確認コードを持つレコードを検索
        $stmt_check_code = $dbh->prepare("SELECT COUNT(*) FROM finalusers WHERE confirmation_code = :confirmation_code");
        $stmt_check_code->bindParam(':confirmation_code', $confirmationCode);
        $stmt_check_code->execute();
        $code_exists = ($stmt_check_code->fetchColumn() > 0);

        if ($code_exists) {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $app_password = $_POST['app_password'];
            $confirmPassword = $_POST['confirm_password'];
            // データベースの該当のレコードに確認コードを記録する
            $stmt_update_code = $dbh->prepare("UPDATE user_confirmation SET is_confirmed = 1 WHERE user_id = :user_id AND confirmation_code = :confirmation_code");
            $stmt_update_code->bindParam(':user_id', $userId); // ユーザーのIDを設定
            $stmt_update_code->bindParam(':confirmation_code', $confirmationCode);


            if ($stmt_update_code->execute()) {

                header("Location: success_page.php"); // 承認済みの場合の遷移先ページ
                // 登録完了メールを送信
        sendRegistrationCompletedEmail($email, $app_password);
                echo "success";
            } else {
                echo "確認コードの検証に失敗しました。";
            }
        } else {
            echo "確認コードが正しくありません。";
        }

        $dbh = null;
    } catch (PDOException $e) {
        echo "データベースに接続できませんでした。";
    }
}
function sendRegistrationCompletedEmail($email, $app_password) {
    // メール送信の処理を実装する
    // ここに実装する必要があります
    // 以下はダミーの実装例
    mb_language('uni');
    mb_internal_encoding('UTF-8');

    $mail = new PHPMailer(true);

    try {
        $host        = 'smtp.gmail.com';
        $username    = $email;
        $password    = $app_password;// AppPasswordを使用
    
        $from        = 'k022c2145@g.neec.ac.jp';
        $fromname    = 'にんにん版';
    
        $to       = $email;
        $toname   = ''; // 受信者名
    
        $subject = '会員登録完了のお知らせ';
        $body    = '会員登録が正式に完了しました。ログインしてご利用ください。';
    
        // メールの送信処理を実装
        // ...
        // デバッグ設定
      //$mail->SMTPDebug = 2; // デバッグ出力を有効化（レベルを指定）
      // $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str<br>";};
    
      // SMTPサーバの設定
      $mail->isSMTP();                          // SMTPの使用宣言
      $mail->Host       = $host;   // SMTPサーバーを指定
      $mail->SMTPAuth   = true;                 // SMTP authenticationを有効化
      $mail->Username   = $username;   // SMTPサーバーのユーザ名
      $mail->Password   = $password;           // SMTPサーバーのパスワード
      $mail->SMTPSecure = 'tls';  // 暗号化を有効（tls or ssl）無効の場合はfalse
      $mail->Port       = 587; // TCPポートを指定（tlsの場合は465や587）
      $mail->setFrom($from, $fromname);
      $mail->addAddress($to, $toname);
      $mail->CharSet = "UTF-8";
      $mail->Encoding="base64";
    
    
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
      $mail->Body    = $body;  
    
      // 送信
      $mail->send();
    } catch (Exception $e) {
        echo "メールの送信に失敗しました。: {$mail->ErrorInfo}";
    }
}

?>
