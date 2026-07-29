<?php
// PDOを使用して、Dockerコンテナ内のMySQLデータベース（example_db）に接続します
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

// POSTリクエストで 'body' というデータが送信されているかを確認します
if (isset($_POST['body'])) {
    
    // データベースへの挿入（INSERT）処理を安全に行うための準備をします
    $insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:body)");
    
    // ユーザーが入力したデータを割り当てて、SQLを実行します
    $insert_sth->execute([
        ':body' => $_POST['body'],
    ]);

    // データの保存処理が完了した後、同じページへリダイレクト（転送）します。
    // この処理を行わないと、ユーザーがブラウザの更新（リロード）ボタンを押した際に、
    // 再度同じデータが送信されてしまう「二重送信（二重投稿）」が発生してしまいます。
    header("HTTP/1.1 302 Found");
    header("Location: ./formtodbtest.php");
    return;
}
?>

<!-- フォームの送信先（action）をこのファイル自身に指定し、POSTメソッドで送信します -->
<form method="POST" action="./formtodbtest.php">
    <textarea name="body"></textarea>
    <button type="submit">送信</button>
</form>
