<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Code Input</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }



            @keyframes gradient {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }


        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 200px;
            width: 100%;
        }
        .title {
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .title-animation {
        animation: slide-up 1s ease-in-out forwards;
        opacity: 0;
        transform: translateY(100%);
    }
    @keyframes slide-up {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>
<body>
    <!-- ローディングアニメーションとタイトルの表示 -->
<div class="loader-container">
    <div class="loader"></div>
    <h2>確認コードを入力してください</h2>
    <form>
        <label for="confirmation_code">確認コード:</label>
        <input type="text" id="confirmation_code" name="confirmation_code" required>
        <button type="button" id="confirmCodeBtn">確認</button>
    </form>
    

    <script>
        const confirmCodeBtn = document.getElementById("confirmCodeBtn"); // 確認ボタンを取得

        // 確認コードの入力ボタンが押された際の処理
confirmCodeBtn.addEventListener("click", async () => {
    const confirmationCodeInput = document.getElementById("confirmation_code"); // 確認コードの入力要素を取得
    if (confirmationCodeInput) {


        const confirmation_code = confirmationCodeInput.value;
        confirmCodeBtn.disabled = true; // ボタンを無効化

        try {
            const verificationResponse = await fetch("verify_confirmation.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ confirmation_code }),
            });

            if (verificationResponse.ok) {
                const verificationResult = await verificationResponse.text();
				console.log("Verification Result:", verificationResult); // コンソールに結果を出力
                if (verificationResult === "success") {
                    // 確認コードが正しく検証された場合、登録完了メールを送信
                    const sendCompleteEmailResponse = await fetch("test_send_email.php", {
                        method: "POST", // テスト用に送信先を test_send_email.php に指定
                });

                    if (sendCompleteEmailResponse.ok) {
                        const sendCompleteEmailResult = await sendCompleteEmailResponse.text();
                        if (sendCompleteEmailResult.includes("success")) {
                            // 正常にメール送信が完了した場合の処理
                            alert("確認コードが正しく検証されました。会員登録が完了しました。");

                            // ログインページに遷移
                            window.location.href = "http://localhost:3000/shop_site/top_page/main.php";
                        } else {
                            // メール送信に失敗した場合の処理
                            alert("メールの送信に失敗しましたよ！！！");
                        }
                    } else {
                        // メール送信に失敗した場合の処理
                        alert("メールの送信に失敗しました！！！");
                    }
                } else {
                    // 確認コードが一致しなかった場合の処理
                    alert("確認コードが正しくありませんね。");
                }
            } else {
                // 通信エラーの場合
                alert("通信エラーが発生しました。");
            }
        } catch (error) {
            alert("エラーが発生しました。");
            console.error("Error:", error);
        } finally {
            confirmCodeBtn.disabled = false; // ボタンを有効化

			// アニメーションを非表示
			loader.style.display = "none";
        }
    } else {
        alert("確認コードを入力してください。");
    }
});
    </script>
</body>
</html>
