<?php
// ユーザ名、投稿内容、添付画像の取得
$username = $_POST['username'];
$content = $_POST['content'];
$image = $_FILES['image'];

// データベースへの保存処理

// 記事一覧の取得
$articles = "記事一覧取得"();

// JSON形式に変換
$json = json_encode($articles);

// クライアントへ送信
echo $json;
?>
