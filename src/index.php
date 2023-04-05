<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>TOP</title>
</head>

<body>
    <h1>フォームサンプル</h1>
    <p>STEP 1 - 入力</p>
    <form action="./confirm.php" method="post">
        <table>
            <tr>
                <th>お名前</th>
                <td><input type="text" name="your_name" value="" /></td>
            </tr>
            <tr>
                <th>問い合わせ内容</th>
                <td><textarea name="content"></textarea></td>
            </tr>
        </table>
        <button type="submit" name="step" value="to_confirm">確認画面へ</button>
    </form>
</body>

</html>