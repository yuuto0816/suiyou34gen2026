<?php
// POSTリクエストで 'body' というデータが送信されているかを確認します
if (isset($_POST['body'])) {

    print('以下の内容を受け取りました!<br>');

    // 送信された内容を出力する際は、クロスサイトスクリプティング（XSS）攻撃を防ぐため、
    // 必ず htmlspecialchars() 関数を使用して特殊文字を無害化（エスケープ）します。
    // nl2br() 関数は、テキストエリアの改行文字をHTMLの <br> タグに変換して改行を反映させます。
    print(nl2br(htmlspecialchars($_POST['body'], ENT_QUOTES, 'UTF-8')));
}
?>

<!-- フォームの送信先（action）をこのファイル自身に指定し、POSTメソッドで送信します -->
<form method="POST" action="./formtest.php">
    <textarea name="body"></textarea>
    <button type="submit">送信</button>
</form>
