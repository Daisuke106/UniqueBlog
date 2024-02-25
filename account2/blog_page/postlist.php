<?php

// データベース接続
$db = new PDO('mysql:host=localhost;dbname=nin_nin_board;charset=utf8mb4', 'root', 'password');

// SQLクエリの実行
$stmt = $db->query('SELECT * FROM articles ORDER BY created_at DESC');

// 記事データの取得
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
  <title>記事一覧</title>
</head>
<body>
  <h1>記事一覧</h1>
  <ul>
    <?php foreach ($articles as $article): ?>
      <li>
        <h2><?php echo $article['username']; ?></h2>
        <p><?php echo $article['content']; ?></p>
        <?php if ($article['image']): ?>
          <img src="./uploads/<?php echo $article['image']; ?>" alt="<?php echo $article['content']; ?>">
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>

