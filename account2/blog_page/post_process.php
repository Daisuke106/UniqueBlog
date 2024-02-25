<?php

// データベース接続
$db = new PDO('mysql:host=localhost;dbname=nin_nin_board;charset=utf8', 'root', 'password');

// 投稿内容の取得
$username = $_POST['username'];
$content = $_POST['content'];
$image = $_FILES['image'];

// 画像ファイルの保存処理
if ($image['size'] > 0) {
  $filename = uniqid() . '.' . pathinfo($image['name'])['extension'];
  move_uploaded_file($image['tmp_name'], './uploads/' . $filename);
} else {
  $filename = null;
}

// SQLクエリの実行
$stmt = $db->prepare('INSERT INTO articles (username, content, image, created_at) VALUES (?, ?, ?, ?)');
$stmt->execute([$username, $content, $filename, date('Y-m-d H:i:s')]);

// 記事一覧ページへリダイレクト
header('Location: index.html');

?>