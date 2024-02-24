    // PHPからのメッセージを表示するためのJavaScriptコード
    const messageElement = document.getElementById('message');
    const messageText = messageElement.textContent.trim();

    if (messageText) {
        alert(messageText);

        if (messageText.includes('仮パスワードをメールで送信しました。Gmailを確認してください。')) {
            // Gmailのウェブサイトを新しいタブで開く
            window.open('https://mail.google.com', '_blank');

            // フォームのウィンドウを閉じる
            window.close();
        }
    }