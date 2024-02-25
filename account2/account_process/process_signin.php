<?php
session_start();
date_default_timezone_set('Asia/Tokyo');
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

require '..\vendor\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'send_lockout_email.php'; // send_lockout_email.php のファイルをインクルード




function sendLoginIssueEmail($email, $username, $app_password, $subject, $message) {
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
    
        $body =  "$username 様\n"
                . "\n"
                . $message . "\n"
                . "\n"
                . "このメールに心当たりがない場合は、このメールを破棄してください。"
                . "\n"
                . 'Your Website Name'
                . "\n"
                . 'http://localhost:3000/shop_site/top_page/main.php'
                . "\n";

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
    
        $mail->Subject = $subject;
        $mail->Body    = $body;
    
        $mail->send();
        echo "メール送信成功";
    } catch (Exception $e) {
        echo "メール送信に失敗しました: " . $mail->ErrorInfo;
    }
}


// generateRandomCode 関数を定義
function generateRandomCode($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}


function sendConfirmationEmail($email, $confirmation_code, $app_password, $username) {
    // PHPMailerの設定とメール送信の処理を実装する
    // 以下はダミーの実装例
    // 文字エンコードを指定
    mb_language('uni');
    mb_internal_encoding('UTF-8');

    $mail = new PHPMailer(true);

    try {

            // // QRコード生成
            // $qrCode = new Endroid\QrCode\QrCode($confirmation_code);
            // $qrCode->setSize(300); // QRコードのサイズを設定
            // $qrCode->setMargin(10); // QRコードの余白を設定

            // // QRコード画像をデータURL形式で取得
            // $qrCodeImageData = $qrCode->writeString();

            // // メールにQRコード画像を添付
            // $mail->addStringAttachment($qrCodeImageData, 'qr_code.png', 'base64');


            // // メールにQRコード画像を添付
            // $mail->addAttachment($qrCodeImagePath);

            // // QRコード画像を保存した後、ファイルを削除
            // unlink($qrCodeImagePath);








        $host        = 'smtp.gmail.com';
        $mailname    = $email;
        $idname      = $username;
        $password    = $app_password;// AppPasswordを使用
    
        $from        = 'k022c2145@g.neec.ac.jp';
        $fromname    = 'にんにん版';
    
        $to = $email;
        $toname = $username;
    
        $subject = '【重要】会員登録の確認コード送付について';
        $body =  "$idname 様\n"
                . "\n"
                . "\n"
                ."にんにん版への会員登録ありがとうございます。" . "\n"
                ."確認コードはログイン時に必要となります。" . "\n"
                . "\n"
                . "以下の確認コードを入力してください。" . "\n"
                . '会員登録の確認コード: ' . $confirmation_code
                . "\n"
                . "\n"
                . 'このコードを先ほどのサイトに入力してください。'
                . "お手数をおかけしますが、よろしくお願いいたします。"
                . "\n"
                . 'このメールに心当たりがない場合は、このメールを破棄してください。'
                . "\n"
                . 'にんにん版'
                . "\n"
                . 'https://www.google.com/'
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







if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $usernameOrId = $_POST['username'];
    $password = $_POST['password'];
    $response = "";

    try {
        $dbh = new PDO($dsn, $db_user, $db_password);
        $stmt = $dbh->prepare("SELECT * FROM finalusers WHERE (username = :username OR id = :username)");
        $stmt->bindParam(':username', $usernameOrId);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // $app_password をデータベースから取得
            $app_password = $user['app_password'];

            $login_count = $user['login_count'] + 1;

            if ($user['is_confirmed'] == 1) {
                $current_time = time();
                $lockout_time = strtotime($user['lockout_time']);
                $unlock_time = strtotime('0 minutes', $lockout_time);

                if ($lockout_time === null || $current_time > $lockout_time) {
                    // パスワードが一致するかチェック
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['username'] = $user['username'];
                        $resetAttemptsStmt = $dbh->prepare("UPDATE finalusers SET failed_attempts = 0, total_failed_attempts = 0, lockout_time = NULL, login_count = login_count + 1 WHERE id = :id");
                        $resetAttemptsStmt->bindParam(':id', $user['id']);
                        $resetAttemptsStmt->execute();

                        // ログイン回数に応じてメール送信をチェック
                        if ($login_count === 20 || $login_count === 50 || $login_count === 100 || $login_count === 200) {
                            $to = $user['email'];
                            $subject = "ログイン回数のお知らせ";
                            $message = "";
                            
                            // 20回ログインが行われた際のメール送信
                            if ($login_count === 20) {
                                $to = $user['email'];
                                $subject = "ログイン回数のお知らせ";
                                $message = "当サイトを20回ご利用いただきありがとうございます。";
                            } elseif ($login_count === 50) {
                                $to = $user['email'];
                                $subject = "ログイン回数のお知らせ";
                                $message = "当サイトのログイン回数50回を記念してお知らせいたします。";
                            } elseif ($login_count === 100) {
                                $message = "100回目のログインを記念してお知らせいたします。";
                            } elseif ($login_count === 200) {
                                $message = "200回目のログインを記念してお知らせいたします。";
                            }

                            sendLoginIssueEmail($to, $user['username'], $app_password, $subject, $message);

                        }

                        $response = "success";
                    } else {
                        // パスワードが一致しない場合
                        // アカウントの連続誤入力回数を増やす
                        $failed_attempts = $user['failed_attempts'] + 1;
                        $total_failed_attempts = $user['total_failed_attempts'] + 1;

                        if ($total_failed_attempts >= 15) {
                            // 15回以上の誤入力の場合、メール送信を行う
                            // sendLoginIssueEmail($to, $user['username'], $app_password, $subject, $message);
                            $to = $user['email'];
                            // メール送信
                            if (sendLockoutEmail($to, $user['username'], $app_password)) {
                                $updateLockoutStmt = $dbh->prepare("UPDATE finalusers SET failed_attempts = 0, total_failed_attempts = 0, lockout_time = NOW() WHERE id = :id");
                                $updateLockoutStmt->bindParam(':id', $user['id']);
                                $updateLockoutStmt->execute();

                                $response = "パスワードが間違っています。メールが送信されました。";
                            } else {
                                $response = "メールの送信に失敗しました。";
                            }

                        } else {
                            // 15回未満の誤入力の場合
                            if ($failed_attempts >= 5) {
                                $lockout_time = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                                $updateStmt = $dbh->prepare("UPDATE finalusers SET failed_attempts = :failed_attempts, total_failed_attempts = :total_failed_attempts, lockout_time = :lockout_time WHERE id = :id");
                                $updateStmt->bindParam(':failed_attempts', $failed_attempts);
                                $updateStmt->bindParam(':total_failed_attempts', $total_failed_attempts);
                                $updateStmt->bindParam(':lockout_time', $lockout_time);
                                $updateStmt->bindParam(':id', $user['id']);
                                $updateStmt->execute();

                                $response = "アカウントが一時的にロックされています。アカウントは " . $lockout_time . " に解除されます。"; // レスポンスメッセージをセット
                            } else {
                                $updateStmt = $dbh->prepare("UPDATE finalusers SET failed_attempts = :failed_attempts, total_failed_attempts = :total_failed_attempts WHERE id = :id");
                                $updateStmt->bindParam(':failed_attempts', $failed_attempts);
                                $updateStmt->bindParam(':total_failed_attempts', $total_failed_attempts);
                                $updateStmt->bindParam(':id', $user['id']);
                                $updateStmt->execute();

                                $remaining_attempts = 5 - $failed_attempts;
                                $response = "パスワードが間違っています。残り試行回数: " . $remaining_attempts; // レスポンスメッセージをセット
                            }
                        }
                    }
                } else {
                    // ロックされている場合
                    $response = "アカウントがロックされています。アカウントは " . date('Y-m-d H:i:s', $unlock_time) . " に解除されます。"; // レスポンスメッセージをセット
                }
            } else {
                // アカウントが未承認の場合
                $response = "success_unconfirmed";

                // 確認コードを生成
                $confirmationCode = generateRandomCode(8);
                
                // 確認コードをデータベースに保存
                $stmt_update_confirmation_code = $dbh->prepare("UPDATE finalusers SET confirmation_code = :confirmation_code WHERE id = :id");
                $stmt_update_confirmation_code->bindParam(':confirmation_code', $confirmationCode);
                $stmt_update_confirmation_code->bindParam(':id', $user['id']);
                $stmt_update_confirmation_code->execute();

                // 確認コードをメールで送信
                $to = $user['email'];
                $subject = "会員登録の確認コード送付について";
                $message = "会員登録の確認コードは: " . $confirmationCode;
                sendConfirmationEmail($to, $confirmationCode, $app_password, $user['username']);
            }
        } else {
            // ユーザーが存在しない場合
            $response = "ユーザーが存在しません。"; // レスポンスメッセージをセット
        }

        $dbh = null;
    } catch (PDOException $e) {
        $response = "データベースに接続できませんでした。" . $e->getMessage();
    }

    echo $response;
}
?>