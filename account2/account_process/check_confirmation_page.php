<!DOCTYPE html>
<html>
<head>
    <title>確認コード入力確認</title>
</head>
<body>
    <h1>確認コード入力確認ページ</h1>
    <form method="post" action="check_confirmation_page.php">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required><br>
        
        <label for="confirmation_code">確認コード:</label>
        <input type="text" id="confirmation_code" name="confirmation_code" required><br>
        
        <button type="submit">確認</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['confirmation_code'])) {
        $username = $_POST['username'];
        $confirmationCode = $_POST['confirmation_code'];

        try {
            $dsn = 'mysql:dbname=sample;host=localhost;charset=utf8';
            $db_user = 'root';
            $db_password = '';

            $dbh = new PDO($dsn, $db_user, $db_password);

            $stmt = $dbh->prepare("SELECT COUNT(*) FROM finalusers WHERE username = :username AND confirmation_code = :confirmation_code");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':confirmation_code', $confirmationCode);
            $stmt->execute();
            $matches = ($stmt->fetchColumn() > 0);

            echo "<p>";
            if ($matches) {
                echo "確認コードが一致しました: OK";
            } else {
                echo "確認コードが一致しません: NG";
            }
            echo "</p>";

            $dbh = null;
        } catch (PDOException $e) {
            echo "<p>データベースに接続できませんでした。</p>";
        }
    }
    ?>
</body>
</html>
