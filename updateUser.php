<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>掲示板</title>
	</head>
	<body>
		<?php
		//mySqlObjのインスタンス生成
		require_once('mySqlObj.php');
		$mySqlObj = new mySqlObj();
		//idに値が設定されている時のみフォームを表示
		if (empty($_GET['id']) && empty($_GET["new_message"])){
			print("該当するスレッドは存在しません</br>");
			print("<a href=\"./testlist.php\">戻る</a>");
		} else {
			$id = htmlspecialchars($_GET['id']);
			?>
			<p>修正フォーム</p>
			<form method="GET" action="updateUser.php">
				<p>本文：</p>
				<?php
					if (!empty($_GET['message'])){
						$old_message = htmlspecialchars($_GET['message']);
						echo '<textarea cols="50" rows="5" name="new_message" MAXLENGTH="1000" >'.$old_message.'</textarea>';
					} else {
						echo '<textarea cols="50" rows="5" name="new_message" MAXLENGTH="1000" ></textarea>';
					}
				?>
				<p>
					<?php
					echo '<input type="hidden" name="id" value="'.$id.'" >'
					?>
					<input type="submit" value="変更" />
					<input type="reset" value="取り消し" />
				</p>
				<a href="./testlist.php">戻る</a>
			</form>
			<?php
				if (!empty($_GET["new_message"])){
					//エスケープしてから使用
					$message = htmlspecialchars($_GET["new_message"]);
					//存在チェック
					$chk_id = $mySqlObj->selectChkId($id);
					if(!empty($chk_id) && ($id == $chk_id)){
						//updateをする
						$mySqlObj->updateMessage($message,$id);
					} else {
						print("該当するスレッドは削除されたか、存在しません<br>");
					}
				}
			}
		?>
	</body>
</html>
