<?php

// Path: practice\final_reference_task\Loginform\send_lockout_email.php

require '..\vendor\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendLockoutEmail($email, $username, $app_password) {
    mb_language('uni');
    mb_internal_encoding('UTF-8');

    $mail = new PHPMailer(true);

    try {
        $host        = 'smtp.gmail.com';
        $mailname    = $email; // Your Gmail address
        $password    = $app_password; // Your Gmail App Password
    
        $from        = 'k022c2145@g.neec.ac.jp';
        $fromname    = 'にんにん版';
    
        $to = $email;
        $toname = $username;
    
        $subject = '【重要】にんにん版ログインに関するお知らせ';
        $body =  "$toname 様\n"
                ."にんにん版をご利用いただきありがとうございます。" . "\n"
                ."\n"
                . "お知らせです。" . "\n"
                . "あなたのアカウントはセキュリティのために一時的にロックされています。" . "\n"
                . "パスワードを15回連続で誤入力されたため、アカウントがロックされました。" . "\n"
                ."\n"
                . "アカウントのロックを解除するためには、お手数ですがお問い合わせページよりお問い合わせください。" . "\n"
                . "以下のリンクからお問い合わせページにアクセスできます。" . "\n"
                ."https://www.example.com/contact" . "\n"
                ."\n"
                . 'にんにん版'
                . "\n"
                . 'https://www.google.com/'
                . "\n";

        // デバッグ設定
        $mail->SMTPDebug = 2; // デバッグ出力を有効化（レベルを指定）
        $mail->Debugoutput = function($str, $level) {
            echo "debug level $level; message: $str<br>";
        };
    
        // SMTPサーバの設定
        $mail->isSMTP();                          // SMTPの使用宣言
        $mail->Host       = $host;   // SMTPサーバーを指定
        $mail->SMTPAuth   = true;                 // SMTP authenticationを有効化
        $mail->Username   = $mailname;   // SMTPサーバーのユーザ名
        $mail->Password   = $password;           // SMTPサーバーのパスワード
        $mail->SMTPSecure = 'tls';  // 暗号化を有効（tls or ssl）無効の場合はfalse
        $mail->Port       = 587; // TCPポートを指定（tlsの場合は465や587）
        $mail->setFrom($from, $fromname);
        $mail->addAddress($to, $toname);
        $mail->CharSet = "UTF-8";
        $mail->Encoding="base64";
    
        // 送信内容設定
        $mail->Subject = $subject; 
        $mail->Body    = $body;  
    
        // 送信
        $mail->send();
        echo "ユーザー: $username に対するメール送信成功<br>"; // 送信成功時にメッセージを返す
    } catch (Exception $e) {
        // エラーの場合
        echo "ユーザー: $username に対するメール送信に失敗しました: " . $mail->ErrorInfo;
        return false;
    }
}
?>
