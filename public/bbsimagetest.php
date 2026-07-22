<?php
// データベース（MySQL）へ接続します
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

// POSTリクエストで 'body' データが送信された場合の処理
if (isset($_POST['body'])) {

    $image_filename = null;
    
    // アップロードされたファイルが存在し、かつ一時ファイルが空ではない場合
    if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
        
        // mime_content_type() を使用してファイルの種類をサーバー側で解析します
        // 画像ファイル（'image/'で始まるMIMEタイプ）ではない場合は処理を強制終了します
        if (preg_match('/^image\//', mime_content_type($_FILES['image']['tmp_name'])) !== 1) {
            header("HTTP/1.1 302 Found");
            header("Location: ./bbsimagetest.php");
            return;
        }

        // アップロードされた元のファイル名から拡張子を取得します
        $pathinfo = pathinfo($_FILES['image']['name']);
        $extension = $pathinfo['extension'];
        
        // 他の画像ファイルと名前が重複しないよう、現在時刻と乱数を組み合わせて新しいファイル名を生成します
        $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;
        $filepath =  '/var/www/upload/image/' . $image_filename;
        
        // 一時ディレクトリから公開用ディレクトリへファイルを移動（保存）します
        move_uploaded_file($_FILES['image']['tmp_name'], $filepath);
    }

    // データベースに投稿本文と画像ファイル名を挿入（保存）します
    $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :image_filename)");
    $insert_sth->execute([
        ':body' => $_POST['body'],
        ':image_filename' => $image_filename,
    ]);

    // 二重送信を防止するため、処理完了後に同じページへリダイレクトします
    header("HTTP/1.1 302 Found");
    header("Location: ./bbsimagetest.php");
    return;
}

// データベースからすべての投稿データを最新順（作成日時の降順）で取得します
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
$select_sth->execute();
?>
<head>
  <title>画像投稿できる掲示板</title>
</head>

<!-- 投稿フォーム：送信先はこのファイル自身に設定し、ファイル送信のために enctype を指定します -->
<form method="POST" action="./bbsimagetest.php" enctype="multipart/form-data">
  <textarea name="body" required></textarea>
  <div style="margin: 1em 0;">
    <input type="file" accept="image/*" name="image" id="imageInput">
  </div>
  <button type="submit">送信</button>
</form>

<hr>

<!-- 取得した投稿データをループ処理で一覧表示します -->
<?php foreach($select_sth as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>ID</dt>
    <dd><?= $entry['id'] ?></dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd>
      <!-- 本文を出力する際は必ず htmlspecialchars() でエスケープ処理を行います -->
      <?= nl2br(htmlspecialchars($entry['body'])) ?>
      
      <!-- 画像のファイル名がデータベースに存在する場合は img 要素で画像を表示します -->
      <?php if(!empty($entry['image_filename'])): ?>
      <div>
        <img src="/image/<?= $entry['image_filename'] ?>" style="max-height: 10em;">
      </div>
      <?php endif; ?>
    </dd>
  </dl>
<?php endforeach ?>

<script>
// HTMLドキュメントの読み込みが完了した時点で処理を実行します
document.addEventListener("DOMContentLoaded", () => {
  const imageInput = document.getElementById("imageInput");
  
  // ファイル選択欄の値が変更された（ファイルが選択された）時のイベントを設定します
  imageInput.addEventListener("change", () => {
    
    // ファイルが選択されていない場合は処理を終了します
    if (imageInput.files.length < 1) {
      return;
    }
    
    // 選択されたファイルのサイズが5MB（5 * 1024 * 1024 バイト）を超える場合の処理です
    if (imageInput.files[0].size > 5 * 1024 * 1024) {
      alert("5MB以下のファイルを選択してください。");
      // 選択状態をリセット（空に）して無効化します
      imageInput.value = "";
    }
  });
});
</script>
