<?php
session_start(); // セッションを開始

// ログイン済みか確認
if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
    // ログイン済みの場合は customer_info.php にリダイレクト
    header('Location: customer_info.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTリクエストからデータを受け取り
    $data = json_decode(file_get_contents("php://input"));

    // カメラからのデータを受け取る
    $scannedId = isset($data->id) ? $data->id : null;

    if ($scannedId) {
        // バックエンドでの認証処理などを行うことができます

        // データベースへの接続情報
        $dbHost = 'localhost';
        $dbName = 'sample';
        $dbUser = 'root';
        $dbPassword = '';

        try {
            // データベースへ接続
            $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // データベースからユーザー情報を取得
            $stmt = $db->prepare("SELECT id, Username, email FROM finalusers WHERE id = :id");
            $stmt->bindParam(':id', $scannedId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // 認証成功
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['email'] = $user['email'];

                $response = [
                    "authenticated" => true,
                    "user" => [
                        "id" => $user['id'],
                        "username" => $user['Username'],
                        "email" => $user['email']
                    ]
                ];
                echo json_encode($response);
            } else {
                // 認証失敗
                echo json_encode(["authenticated" => false]);
            }
        } catch (PDOException $e) {
            echo json_encode(["authenticated" => false, "error" => $e->getMessage()]);
        }
    } else {
        // 認証失敗またはエラーの場合
        echo json_encode(["authenticated" => false]);
    }
    exit; // POSTリクエスト処理後にスクリプトを終了
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Login</title>
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

        video {
            width: 100%;
            max-width: 640px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h2>QR Login</h2>
    <h3>カメラを起動してQRコードをスキャンしてください。</h3>
    <br>
    <h3><a href="../top_page/index.html">ブログサイトに戻る</a></h3>
    <br>
    <link rel="icon" href="../logo/garlic.html">

    <!-- ログイン成功時のメッセージ表示エリア -->
    <div id="loginMessage" style="display: none;"></div>

    <video id="camera" autoplay></video>

    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>

        // カメラへのアクセスを要求する関数
        function requestCameraAccess() {
            const constraints = {
                video: {
                    facingMode: "environment" // カメラのフロントまたはリアカメラを選択（必要に応じて変更）
                }
            };

            return navigator.mediaDevices.getUserMedia(constraints);
        }

        // カメラアクセスのリクエスト
        requestCameraAccess()
            .then(function (stream) {
                // カメラがアクセスできた場合の処理
                const videoElement = document.getElementById('camera');
                videoElement.srcObject = stream;
                videoElement.play();
            })
            .catch(function (error) {
                // カメラへのアクセスが拒否された場合やエラーが発生した場合の処理
                console.error('カメラへのアクセスが拒否されました。', error);
            });

        const scanner = new Instascan.Scanner({ video: document.getElementById('camera') });

        scanner.addListener('scan', function (content) {
            // QRコードから読み取った内容を取得
            const scannedId = content.trim();

            // バックエンドにスキャンしたIDを送信し、認証を行う
            fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', { // 自分自身のURLにPOSTリクエストを送信
                method: 'POST',
                body: JSON.stringify({ id: scannedId }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.authenticated) {
                    // ログインに成功した場合の処理
                    const loginMessage = document.getElementById('loginMessage');
                    loginMessage.style.display = 'block';
                    alert(`${data.user.username}さん。ログインに成功しました！\nEmail: ${data.user.email}`); // ログイン成功のアラートを表示

                    // ユーザーIDをセッションに設定
                    const userId = data.user.id;
                    window.location.href = 'customer_info.php'; // customer_info.php にリダイレクト
                } else {
                    // 認証失敗またはその他のエラーの場合
                    alert('ログインに失敗しました。');
                }
            })
            .catch(error => {
                console.error('エラー:', error);
            });
        });

        Instascan.Camera.getCameras()
            .then(cameras => {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('カメラが見つかりません。');
                }
            })
            .catch(error => {
                console.error('カメラの取得中にエラーが発生しました:', error);
            });
    </script>
</body>
</html>
