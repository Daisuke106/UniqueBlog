<?php
// データベースから記事一覧を取得

// JSON形式に変換
$json = json_encode($articles);
// クライアントへ送信
echo $json;
?>
