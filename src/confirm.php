<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>TOP</title>
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
        </table>
        <input type="hidden" name="your_name" value="<?php echo $_POST['your_name']; ?>" />
        <input type="hidden" name="content" value="<?php echo $_POST['content']; ?>" />
        <button type="submit" name="step" value="send">送信</button>
    </form>
</body>

</html>