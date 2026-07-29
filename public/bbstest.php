<?php
// データベース（MySQL）へ接続します
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

// フォームからPOSTメソッドで 'body' データが送信された場合の処理
if (isset($_POST['body'])) {
    
    // 入力された内容をデータベースの bbs_entries テーブルに挿入（保存）する準備をします
    $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body) VALUES (:body)");
    
    // ユーザーの入力データを割り当ててSQLを実行します
    $insert_sth->execute([
        ':body' => $_POST['body'],
    ]);

    // データの保存処理完了後、現在のページへリダイレクト（転送）します
    // ※ブラウザの更新（リロード）によるデータの二重送信を防止するための必須処理です
    header("HTTP/1.1 302 Found");
    header("Location: ./bbstest.php");
    return;
}

// データベースに保存されているすべての投稿データを、作成日時の降順（最新順）で取得します
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
$select_sth->execute();
?>

<form method="POST" action="./bbstest.php">
    <textarea name="body"></textarea>
    <button type="submit">送信</button>
</form>

<hr>

<?php foreach($select_sth as $entry): ?>
    <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
        <dt>ID</dt>
        <dd><?= $entry['id'] ?></dd>
        
        <dt>日時</dt>
        <dd><?= $entry['created_at'] ?></dd>
        
        <dt>内容</dt>
        <dd><?= nl2br(htmlspecialchars($entry['body'], ENT_QUOTES, 'UTF-8')) ?></dd>
    </dl>
<?php endforeach; ?>
