<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/file-operator.php';

use PHPMailer\PHPMailer;

$mail = new PHPMailer\PHPMailer(true);
$file_operator = FileOperator::get_instance();

$mail->CharSet = 'UTF-8';
$mail->setFrom('from@example.com', 'サンプルフォーム');
$mail->addAddress('admin@example.com');
$mail->Subject = 'テストフォームから問い合わせがありました。';

$body = "";
$body .= "{$_POST['your_name']}さんから以下の問い合わせがありました。\n";
$body .= "---\n";
$body .= $_POST['content'];
$mail->Body    = $body;

//confirm.phpの段階で一時ディレクトリに保存したファイルを、アップロードされた際のファイル名で、メールに添付
$mail->addAttachment($file_operator->get_file_path($_POST['tmp_file_name']), $_POST['attachment_file']);

$mail->send();

//送信処理完了後、一時ディレクトリからファイルを消す
$file_operator->del_file($_POST['tmp_file_name']);

//確認画面まで進んで帰ったユーザーがいた場合、一時ディレクトリにファイルが残る事になるので、それを一定時間毎に消す為の処理も動かす
$file_operator->garbage_collection();
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