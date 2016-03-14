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
	//mySqlObjのインスタンス生成
	require_once('mySqlObj.php');
	$mySqlObj = new mySqlObj();
	//名前とメールアドレスがPOSTされているなら
	if(!empty($_POST["name"]) && !empty($_POST["address"])){
		//エスケープしてから引数設定
		$name = htmlspecialchars($_POST["name"]);
		$mailaddress = htmlspecialchars($_POST["address"]);
		$comment = htmlspecialchars($_POST["message"]);
		$mySqlObj->insertUserInfo($name, $mailaddress, $comment);
	} 
	?>
  </body>
</html>
