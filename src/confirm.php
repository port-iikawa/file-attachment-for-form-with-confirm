<?php
require __DIR__ . '/file-operator.php';
$file_operator = FileOperator::get_instance();
$upload_file_names = $file_operator->upload_file();
?>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>入力内容確認</title>
</head>

<body>
    <h1>フォームサンプル</h1>
    <p>STEP 2 - 確認</p>
    <form action="./send.php" method="post">
        <table>
            <tr>
                <th>お名前</th>
                <td><?php echo $_POST['your_name']; ?></td>
            </tr>
            <tr>
                <th>問い合わせ内容</th>
                <td><?php echo nl2br($_POST['content']); ?></td>
            </tr>
            <tr>
                <th>添付ファイル</th>
                <td><?php echo $upload_file_names['upload_file_name']; ?></td>
            </tr>
        </table>
        <input type="hidden" name="your_name" value="<?php echo $_POST['your_name']; ?>" />
        <input type="hidden" name="content" value="<?php echo $_POST['content']; ?>" />
        <input type="hidden" name="attachment_file" value="<?php echo $upload_file_names['upload_file_name']; ?>" />
        <input type="hidden" name="tmp_file_name" value="<?php echo $upload_file_names['tmp_file_name']; ?>" />
        <button type="submit" name="step" value="send">送信</button>
    </form>
</body>

</html>