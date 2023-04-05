<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer;

$mail = new PHPMailer\PHPMailer(true);
$mail->CharSet = 'UTF-8';

$mail->setFrom('from@example.com', 'サンプルフォーム');
$mail->addAddress('admin@example.com');  

$mail->Subject = 'テストフォームから問い合わせがありました。';

$body = "";
$body .= "{$_POST['your_name']}さんから以下の問い合わせがありました。\n";
$body .= "---\n";
$body .= $_POST['content'];
$mail->Body    = $body;


$mail->send();
?>

<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>送信完了</title>
</head>

<body>
    <h1>フォームサンプル</h1>
    <p>STEP 3 - 完了</p>
</body>

</html>