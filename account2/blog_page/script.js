const 投稿エリア = document.getElementById('投稿エリア');
const 投稿フォーム = document.getElementById('投稿フォーム');
const 記事一覧 = document.getElementById('記事一覧');
const 投稿者名 = document.getElementById('投稿者名');

// ログイン状態チェック
if (ログイン状態 === true) {
  投稿エリア.classList.remove('hidden');
  投稿者名.textContent = 'ようこそ、' + ユーザ名 + 'さん';
} else {
  投稿エリア.classList.add('hidden');
  投稿者名.textContent = 'ログインが必要です';
}
const 新規記事作成ボタン = document.getElementById('新規記事作成ボタン');

新規記事作成ボタン.addEventListener('click', function() {
  投稿エリア.classList.remove('hidden');
});
// 記事一覧の取得
function 記事一覧取得() {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', '記事一覧取得.php');
  xhr.onload = function() {
    if (xhr.status === 200) {
      記事一覧.innerHTML = xhr.responseText;
    } else {
      console.error('記事一覧の取得に失敗しました');
    }
  };
  xhr.send();
}

// 記事投稿
投稿フォーム.addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(投稿フォーム);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'post_process.php');
  xhr.onload = function() {
    if (xhr.status === 200) {
      記事一覧取得();
      投稿フォーム.reset();
    } else {
      console.error('記事の投稿に失敗しました');
    }
  };
  xhr.send(formData);
});

// 30秒間隔で記事一覧を自動リロード
setInterval(記事一覧取得, 30000);
記事一覧取得();