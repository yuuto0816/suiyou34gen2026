<?php
// PDOを使って、コンテナ名「mysql」のデータベース「example_db」に接続します
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

// テーブル「hogehoge」の「text」カラムにデータを挿入する準備をします
$insert_sth = $dbh->prepare("INSERT INTO hogehoge (text) VALUES (:text)");

// 実際にデータを挿入（実行）します
$insert_sth->execute([
    ':text' => 'hello world!!!!!!!!!'
]);

// 画面に完了メッセージを表示します
print('insertできました');
