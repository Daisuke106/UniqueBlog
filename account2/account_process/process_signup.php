<?php
session_start(); // セッションを開始
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

require '..\vendor\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['app_password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $app_password = $_POST['app_password'];
    $confirmPassword = $_POST['confirm_password'];

    // パスワードの条件をチェック
    if (strlen($password) < 6 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        echo "パスワードは6文字以上で、英大文字と小文字、数字を含む必要があります。";
    } elseif ($password !== $confirmPassword) {
        echo "パスワードが一致しません。";
    } else {
        try {
            $dbh = new PDO($dsn, $db_user, $db_password);
        
            // メールアドレスの重複チェック
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM finalusers WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $email_exists = ($stmt->fetchColumn() > 0);

        
            if ($email_exists) {
                echo "このメールアドレスは既にご登録済みです。";
            } else {
                // 生成した確認コード
                $confirmation_code = generateRandomCode();
        
                // パスワードをハッシュ化
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // $app_password = password_hash($app_password, PASSWORD_DEFAULT);
        
                $id = mt_rand(100000, 999999);
                $time = date("Y-m-d H:i:s");
        
                $stmt = $dbh->prepare("INSERT INTO finalusers (id, username, email, password, app_password, time, confirmation_code) VALUES (:id, :username, :email, :password, :app_password, :time, :confirmation_code)");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':app_password', $app_password);
                $stmt->bindParam(':time', $time);
                $stmt->bindParam(':confirmation_code', $confirmation_code);
        
                try {
                    if ($stmt->execute()) {
                        // 確認メールを送信
                        sendConfirmationEmail($email, $confirmation_code, $app_password, $username);
            
                        // セッションに確認コードを保存
                        $_SESSION['confirmation_code'] = $confirmation_code;
            
                        echo "success";
                    } else {
                        echo "会員登録に失敗しました。";
                    }
                } catch (PDOException $e) {
                    echo "データベースに接続できませんでした。";
                }
            }
            
        } catch (PDOException $e) {
            echo "データベースに接続できませんでした。";
        }
    }
}

// 確認メールを送信する関数
function sendConfirmationEmail($email, $confirmation_code, $app_password, $username) {
    // PHPMailerの設定とメール送信の処理を実装する
    // 以下はダミーの実装例
    // 文字エンコードを指定
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
    
        $subject = '会員登録の確認コード送付について';
        $body =  "$idname 様\n"
                . "\n"
                . "\n"
                ."にんにん版への会員登録ありがとうございます。" . "\n"
                . "\n"
                . '会員登録の確認コード: ' . $confirmation_code
                . "\n"
                . "\n"
                . 'このコードを先ほどのサイトに入力してください。'
                . "\n"
                . 'このメールに心当たりがない場合は、このメールを破棄してください。'
                . "\n"
                . 'にんにん版'
                . "\n"
                . 'http://localhost:3000/shop_site/top_page/main.php'
                . "\n";
    
    
    
    
    
      // デバッグ設定
      //$mail->SMTPDebug = 2; // デバッグ出力を有効化（レベルを指定）
      // $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str<br>";};
    
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
        // エラーの場合
        echo "メールの送信に失敗しました。: {$mail->ErrorInfo}";
    }
}

// 適切なコード生成関数を実装する
function generateRandomCode($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $code;
}
?>
