<?php
session_start(); // セッションを開始

// ログアウトボタンがクリックされた場合
if (isset($_POST['logout'])) {
    // セッションを削除
    session_unset();
    session_destroy();

    // main.phpのセッションも削除
    if (isset($_SESSION['loggedIn'])) {
        unset($_SESSION['loggedIn']);
    }
    if (isset($_SESSION['loginTime'])) {
        unset($_SESSION['loginTime']);
    }

    // main.phpにリダイレクト
    header('Location: ../top_page/index.html');
    exit;
}

// アカウント削除ボタンがクリックされた場合
if (isset($_POST['delete'])) {
    // JavaScriptを使用して確認ダイアログを表示し、確認後に削除を行う
    echo '<script>
        var confirmDelete = confirm("本当にアカウントを削除しますか？");
        if (confirmDelete) {
            // OKボタンがクリックされた場合、アカウントを削除するためのリクエストを送信
            window.location.href = "delete_account.php";
        }
    </script>';
}

// ログイン済みか確認
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    // ログインしていない場合はログインページにリダイレクト
    header('Location: qr_login.php');
    exit;
}

// セッションからユーザー情報を取得
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// データベースに接続
$dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
$db_user = 'root';
$db_password = '';

try {
    $dbh = new PDO($dsn, $db_user, $db_password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ユーザーの time カラムと login_count カラムの情報を取得
    $stmt = $dbh->prepare("SELECT time, login_count FROM finalusers WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 認証成功
        $time = $user['time'];
        $loginCount = $user['login_count'];
    } else {
        // データが見つからない場合の処理
        $time = "情報なし";
        $loginCount = "情報なし";
    }
} catch (PDOException $e) {
    // データベースエラーの処理
    $time = "データベースエラー";
    $loginCount = "データベースエラー";
}

// ユーザーの追加情報を初期化
$userAddress = [
    'postal_code' => '',
    'prefecture' => '',
    'city' => '',
    'address1' => '',
    'address2' => '',
    'tel' => ''
];

// 登録情報が既に登録されているか確認
try {
    $stmt = $dbh->prepare("SELECT * FROM finalusersaddress WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $userAddress = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userAddress) {
        // 登録情報が登録されていない場合、デフォルトの値を設定
        $userAddress = [
            'postal_code' => '',
            'prefecture' => '',
            'city' => '',
            'address1' => '',
            'address2' => '',
            'tel' => ''
        ];
    }
} catch (PDOException $e) {
    // データベースエラーの処理
    $error_message = "データベースエラー: " . $e->getMessage();
}

// ログイン済みか確認
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    // ログインしていない場合はログインページにリダイレクト
    header('Location: qr_login.php');
    exit;
}

// セッションからユーザー情報を取得
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// 登録情報フォームの表示制御
$showAddInfoForm = empty($userAddress['postal_code']);

// フォームが送信された場合
if (isset($_POST['add_info'])) {
    // フォームからのデータを取得
    $postalCode = $_POST['postal_code'];
    $prefecture = $_POST['prefecture'];
    $city = $_POST['city'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $tel = $_POST['tel'];

    // データベースに登録または更新
    try {
        if (empty($userAddress['postal_code'])) {
            // ユーザーの追加情報がまだ登録されていない場合、新規登録
            $stmt = $dbh->prepare("INSERT INTO finalusersaddress (user_id, postal_code, prefecture, city, address1, address2, tel) VALUES (:user_id, :postal_code, :prefecture, :city, :address1, :address2, :tel)");
        } else {
            // すでに登録されている場合、更新
            $stmt = $dbh->prepare("UPDATE finalusersaddress SET postal_code = :postal_code, prefecture = :prefecture, city = :city, address1 = :address1, address2 = :address2, tel = :tel WHERE user_id = :user_id");
        }

        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':prefecture', $prefecture);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':address1', $address1);
        $stmt->bindParam(':address2', $address2);
        $stmt->bindParam(':tel', $tel);
        $stmt->execute();

        // メッセージを設定
        $infoMessage = "登録情報が保存されました。";

        // 登録情報フォームを非表示にする
        $showAddInfoForm = false;
    } catch (PDOException $e) {
        // エラーメッセージを設定
        $error_message = "データベースエラー: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .info-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .add-info-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
            <?php if (!$showAddInfoForm) : ?>
                display: none;
            <?php endif; ?>
        }

        input[type="text"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .show-form-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Customer Info</h2>
    <div class="info-container">
        <table>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($username); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($email); ?></td>
            </tr>
            <tr>
                <th>Registered Date</th>
                <td><?php echo htmlspecialchars($time); ?></td>
            </tr>
            <tr>
                <th>Login Count</th>
                <td><?php echo htmlspecialchars($loginCount); ?></td>
            </tr>
        </table>
        <form action="customer_info.php" method="post">
            <button type="submit" name="logout">ログアウト</button>
            <button type="submit" name="delete">アカウント削除</button>
        </form>
    </div>

    <!-- 登録情報追加フォーム -->
    <div class="add-info-container">
        <h3>登録情報を追加</h3>
        <form action="customer_info.php" method="post">
            <input type="text" name="postal_code" placeholder="郵便番号" value="<?php echo htmlspecialchars($userAddress['postal_code']); ?>" required><br>
            <input type="text" name="prefecture" placeholder="都道府県" value="<?php echo htmlspecialchars($userAddress['prefecture']); ?>" required><br>
            <input type="text" name="city" placeholder="市区町村" value="<?php echo htmlspecialchars($userAddress['city']); ?>" required><br>
            <input type="text" name="address1" placeholder="住所1" value="<?php echo htmlspecialchars($userAddress['address1']); ?>" required><br>
            <input type="text" name="address2" placeholder="住所2" value="<?php echo htmlspecialchars($userAddress['address2']); ?>"><br>
            <input type="tel" name="tel" placeholder="電話番号" value="<?php echo htmlspecialchars($userAddress['tel']); ?>" required><br>
            <button type="submit" name="add_info">登録</button>
        </form>
    </div>

    <!-- 登録情報追加フォームを表示するボタン -->
    <?php if ($showAddInfoForm) : ?>
        <button class="show-form-button" onclick="toggleAddInfoForm()">登録情報を追加</button>
    <?php endif; ?>

    <!-- 登録情報追加成功メッセージ -->
    <?php if (isset($infoMessage)) : ?>
        <p><?php echo $infoMessage; ?></p>
    <?php endif; ?>

    <script>
        // 登録情報追加フォームの表示切り替え
        function toggleAddInfoForm() {
            var addInfoForm = document.querySelector('.add-info-container');
            addInfoForm.style.display = addInfoForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
