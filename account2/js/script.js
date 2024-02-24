
function openResetPasswordWindow() {
    // 別のウィンドウを開く
    window.open("forgot_password.php", "_blank", "width=500, height=400");
}


function showAlert(message) {
alert(message);
document.getElementById("password").value = "";
document.getElementById("confirm_password").value = "";
document.getElementById("app_password").value = "";
}


const testFinalUsersConnectionBtn = document.querySelector("#test-finalusers-connection-btn");

testFinalUsersConnectionBtn.addEventListener("click", async () => {
try {
const response = await fetch("test_finalusers_connection.php");
if (response.ok) {
    const result = await response.text();
    if (result === "success") {
        alert("finalusersテーブルに正しく接続されています");
    } else {
        alert("finalusersテーブルに接続されていません");
    }
} else {
    alert("通信エラーが発生しました");
}
} catch (error) {
alert("エラーが発生しました");
}
});

const testConnectionBtn = document.querySelector("#test-connection-btn");

testConnectionBtn.addEventListener("click", async () => {
try {
const response = await fetch("test_db_connection.php");
if (response.ok) {
    const result = await response.text();
    if (result === "success") {
        alert("正しく接続されています");
    } else {
        alert("接続されていません");
    }
} else {
    alert("通信エラーが発生しました");
}
} catch (error) {
alert("エラーが発生しました");
}
});



const createTableBtn = document.getElementById("createTableBtn");

createTableBtn.addEventListener("click", async () => {
try {
const response = await fetch("create_finalusers_table.php");

if (response.ok) {
    const result = await response.text();
    if (result.includes("既にテーブルが存在します。")) {
        alert("既にテーブルが存在します。");
    } else if (result.includes("テーブルが作成されました。")) {
        alert("finalusersテーブルが正常に作成されました。");
    } else {
        alert("テーブルの作成に失敗しました。");
    }
} else {
    alert("通信エラーが発生しました。");
}
} catch (error) {
alert("エラーが発生しました。");
}
});






const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");
const signinForm = document.querySelector(".sign-in-form"); // Sign In フォーム
const signupForm = document.querySelector(".sign-up-form"); // Sign Up フォーム

const loginForm = document.querySelector("#login-form");
const loginButton = document.querySelector("#login-form-submit");
const loginErrorMsg = document.querySelector("#login-error-msg");

const signUpForm = document.querySelector(".sign-up-form");
const signUpButton = document.querySelector("#sign-up-form-submit");
const signUpErrorMsg = document.querySelector("#sign-up-error-msg");

// ポップアップ表示トリガーボタン
const appPasswordLink = document.getElementById("app-password-link");
const appPasswordPopup = document.getElementById("appPasswordPopup");
const closePopupBtn = appPasswordPopup.querySelector(".close-popup");

// リンクをクリックしてポップアップを表示
appPasswordLink.addEventListener("click", () => {
appPasswordPopup.style.display = "block";
});

// ポップアップを閉じる
closePopupBtn.addEventListener("click", () => {
appPasswordPopup.style.display = "none";
});



// 外側のスコープで email 変数を宣言
let email;
let username;
let app_password;
let id;
let confirmation_code;


document.addEventListener("DOMContentLoaded", () => {
const emailInput = document.querySelector('input[name="email"]');
const signUpForm = document.querySelector(".sign-up-form");
const emailError = document.querySelector("#email-error");

signUpForm.addEventListener("submit", (event) => {
const email = emailInput.value;
const emailParts = email.split('@');
const domain = emailParts[1];

if (domain !== "g.neec.ac.jp") {
    event.preventDefault(); // フォーム送信を阻止
    emailError.textContent = "学校から配布されたアドレスを使用してください";
    emailError.style.display = "block";
}
});

emailInput.addEventListener("input", () => {
emailError.style.display = "none";
});
});

document.getElementById("signup-form").addEventListener("submit", function(event) {
var password = document.getElementById("password").value;
var confirmPassword = document.getElementById("confirm_password").value;

if (password !== confirmPassword) {
event.preventDefault();
alert("パスワードが一致していません");
}
});





// 会員登録フォームが送信された際の処理
signupForm.addEventListener("submit", async (event) => {
event.preventDefault();

// アニメーション要素を取得
const loader = document.getElementById("loader");
// アニメーションを表示
loader.style.display = "block";

const formData = new FormData(signupForm);
const response = await fetch("process_signup.php", {
method: "POST",
body: formData,
});

if (response.ok) {

// アニメーションを非表示
loader.style.display = "none";
const result = await response.text();
if (result === "success") {
    alert("会員登録が完了しました。確認コードが送信されました。");

    // Gmailのウェブサイトを新しいタブで開く
    window.open('https://mail.google.com', '_blank');

    // モーダルウィンドウを表示
    const modal = document.getElementById("myModal");
    modal.style.display = "block";

    const confirmationCodeInput = document.getElementById("confirmation_code");
    const confirmCodeBtn = document.getElementById("confirmCodeBtn");

} else {
    if (result === "このメールアドレスは既にご登録済みです。") {
        alert("既にご登録済みです。ログインページへお進みください。");
    } else {
        // エラーメッセージを表示
        alert(result);
    }
}

    

// 確認コードの入力ボタンが押された際の処理
confirmCodeBtn.addEventListener("click", async () => {
const confirmationCodeInput = document.getElementById("confirmation_code"); // 確認コードの入力要素を取得
if (confirmationCodeInput) {

// ローディングアニメーション表示
const loader = document.getElementById("loader");
// アニメーションを表示
loader.style.display = "block";

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


} else {
const errorMessage = "通信エラーが発生しました。";
try {
    const errorResponse = await response.text();
    if (errorResponse) {
        alert(errorMessage + "\nエラー詳細: " + errorResponse);
    } else {
        alert(errorMessage);
    }
} catch (error) {
    alert(errorMessage);
    console.error("Error:", error);
}
}
});



// ログインボタンが押された際の処理
signinForm.addEventListener("submit", async (event) => {
event.preventDefault();



// ログイン処理

    // ログイン成功の場合
const formData = new FormData(signinForm);
const response = await fetch("process_signin.php", {
    method: "POST",
    body: formData
});

if (response.ok) {
const result = await response.text();
const resultArray = result.split("|"); // レスポンスを分割

if (resultArray[0] === "success") {
    // ログイン成功時の処理
    window.location.href = "http://localhost:3000/shop_site/top_page/main.php"; // ダッシュボードページへリダイレクト
} else if (resultArray[0] === "success_unconfirmed") {
    alert("アカウントが未承認です。確認コードを入力してください。メールをお送りします。");// アカウント未承認時のレスポンス
    // 会員登録成功かつ未確認の場合、確認コード入力ページに遷移
    window.location.href = "confirmation_input_page.php";

    // Gmailのウェブサイトを新しいタブで開く
    window.open('https://mail.google.com', '_blank');
} else if (!result.includes("メール送信成功")) {
    alert(resultArray[0]); // エラーメッセージを表示
}
} else {
alert("ログインに失敗しました。");
}
});









sign_up_btn.addEventListener("click", () => {
container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
container.classList.remove("sign-up-mode");
});
