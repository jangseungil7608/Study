<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>掲示板</title>
  </head>
  <body>
    <p>投稿フォーム</p>
    <form method="POST" action="post.php">
        <p>名前：(必須)</p>
              <input name="name" size = "20" MAXLENGTH = "20"/>
        <p>メールアドレス：(必須)</p>
              <input name="address" size = "50" MAXLENGTH = "64"/>
        <p>本文：</p>
              <textarea cols="50" rows="5" name="message" MAXLENGTH = "1000"></textarea>
        <p>
            <input type="submit" value="送信" />
            <input type="reset" value="取り消し" />
        </p>
        <a href="./testlist.php">戻る</a>
    </form>
    <?php
      //名前とメールアドレスがPOSTされているなら
      if(!empty($_POST["name"]) && !empty($_POST["address"])){
        //エスケープしてから表示
        $name = htmlspecialchars($_POST["name"]);
        $mailaddress = htmlspecialchars($_POST["address"]);
        $comment = htmlspecialchars($_POST["message"]);

        //mysqliクラスのオブジェクトを作成
        $mysqli = new mysqli('localhost', 'root', '', 'testbase');
        //エラーが発生したら
        if ($mysqli->connect_error){
            print("接続失敗：" . $mysqli->connect_error);
            exit();
        }
        //プリペアドステートメントを作成　ユーザ入力を使用する箇所は?にしておく
        $stmt = $mysqli->prepare("INSERT INTO userinfo (name, address, message) VALUES (?, ?, ?)");
        //$_POST["name"]に名前が、$_POST["message"]にアドレスが、$_POST["message"]に本文が格納されているとする。
        //?の位置に値を割り当てる
        $stmt->bind_param('sss', $name, $mailaddress, $comment);
        //実行
        $stmt->execute();

	//userinfoテーブルから登録日付の降順でデータを取得
	$result = $mysqli->query("SELECT * FROM userinfo ORDER BY created DESC LIMIT 1");
	if($result){
	  //1行ずつ取り出し
	  while($row = $result->fetch_object()){
	    //エスケープして表示
            $id = htmlspecialchars($row->id);
	    $name = htmlspecialchars($row->name);
	    $message = htmlspecialchars($row->message);
	    $created = htmlspecialchars($row->created);
	    print("登録内容<br>");
	    print("$id: $name : <br>$message <br>($created)<br>");
	  }
	}
        // DBを閉じる
        $mysqli->close();
      } 
    ?>
  </body>
</html>
