<?php

class mySqlObj {

	private $mysqli;

	public function __construct() {
		//mysqliクラスのオブジェクトを作成
		$this->mysqli = new mysqli('localhost', 'root', '', 'testbase');
		if ($this->mysqli->connect_error){
			print("接続失敗：" . $this->mysqli->connect_error);
			exit();
		}
	}

	public function selectUserCount() {		
		//userinfoテーブル内の件数を取得
		$all_count = $this->mysqli->query("SELECT COUNT(*) cnt  FROM userinfo");
		if($all_count){
			while($cont = $all_count->fetch_object()){
				$cnt = htmlspecialchars($cont->cnt);
				return $cnt;
			}
		}
		// DBを閉じる
		$this->mysqli->close();
	}

	public function selectBulletinBoard($now_page,$max_page) {
		//userinfoテーブルから登録日付の降順でデータを取得
		$result = $this->mysqli->query("SELECT * FROM userinfo ORDER BY created LIMIT 10");
		if($result){
			//1行ずつ取り出し
			while($row = $result->fetch_object()){
			//エスケープして表示
			$id = htmlspecialchars($row->id);
			$name = htmlspecialchars($row->name);
			$message = htmlspecialchars($row->message);
			$created = htmlspecialchars($row->created);
			$userdate = array('id' => $id, 'message' => $message);
			print("<b>$id: $name : </b><br>");
			print("$message <br>");
			print("($created)<br>");
			echo '<a href="./updateUser.php?'.http_build_query($userdate, '', '&amp;').'">変更</a> ';
			echo '<a href="testlist.php?page='.$now_page.'&maxpage='.$max_page.'&'.http_build_query($userdate, '', '&amp;').'&mode=del">削除</a><br>';
			}
		}
		// DBを閉じる
		$this->mysqli->close();
	}

	public function selectBulletinBoardNext($now_page,$max_page) {
		//userinfoテーブルから登録日付の降順でデータを取得
		$offset = ($now_page * 10) - 10;
		$result = $this->mysqli->query("SELECT * FROM userinfo ORDER BY created LIMIT 10 OFFSET ".$offset);
		if($result){
		  //1行ずつ取り出し
		  while($row = $result->fetch_object()){
			//エスケープして表示
			$id = htmlspecialchars($row->id);
			$name = htmlspecialchars($row->name);
			$message = htmlspecialchars($row->message);
			$created = htmlspecialchars($row->created);
			$userdate = array('id' => $id, 'message' => $message);
			print("<b>$id: $name : </b><br>");
			print("$message <br>");
			print("($created)<br>");
			echo '<a href="./updateUser.php?'.http_build_query($userdate, '', '&amp;').'">変更</a>    ';
			echo '<a href="testlist.php?page='.$now_page.'&maxpage='.$max_page.'&'.http_build_query($userdate, '', '&amp;').'&mode=del">削除</a><br>';
		  }
		}
		// DBを閉じる
		$this->mysqli->close();
	}

	public function selectChkId($id) {
		$result = $this->mysqli->query("SELECT id FROM userinfo where id = ".$id."");
		if($result){
			//1行取り出し
			while($row = $result->fetch_object()){
				//エスケープして表示
				$chk_id = htmlspecialchars($row->id);
				return $chk_id;
			}
		}
		// DBを閉じる
		$this->mysqli->close();
	}

	public function insertUserInfo($name, $mailaddress, $comment) {
		try{
			//オートコミットをOFF
			$stmt = $this->mysqli->autocommit(false);
			//トランザクション開始
			$stmt = $this->mysqli->begin_transaction();
			//プリペアドステートメントを作成　ユーザ入力を使用する箇所は?にしておく
			$stmt = $this->mysqli->prepare("INSERT INTO userinfo (name, address, message) VALUES (?, ?, ?)");
			//?の位置に値を割り当てる
			$stmt->bind_param('sss', $name, $mailaddress, $comment);
			//実行
			$stmt->execute();
			//コミットをする
			$this->mysqli->commit();
			$this->createHistory();
		} catch(Exception $e) {
			//ロールバックする
			$this->mysqli->rollback();
			throw $e;
		}
		// DBを閉じる
		$stmt->close();
	}

	public function updateMessage($message,$id) {
		try{
			//オートコミットをOFF
			$stmt = $this->mysqli->autocommit(false);
			//トランザクション開始
			$stmt = $this->mysqli->begin_transaction();
			//プリペアドステートメントを作成　ユーザ入力を使用する箇所は?にしておく
			$stmt = $this->mysqli->prepare("UPDATE userinfo set message = ? where id = ?");
			//?の位置に値を割り当てる
			$stmt->bind_param('si', $message, $id);
			//実行
			$stmt->execute();
			//コミットをする
			$this->mysqli->commit();
			$this->changeHistory($id);
		} catch(Exception $e) {
			var_dump($e);
			//ロールバックする
			$this->mysqli->rollback();
			throw $e;
		}
		// DBを閉じる
		$stmt->close();
		//$result->mysqli->close();
	}

	public function deleteUser($id) {
		try{
			//オートコミットをOFF
			$stmt = $this->mysqli->autocommit(false);
			//トランザクション開始
			$stmt = $this->mysqli->begin_transaction();
			//userinfoテーブル内の該当レコードを削除
			$stmt = $this->mysqli->prepare("DELETE FROM userinfo WHERE id = ?");
			//?の位置に値を割り当てる
			$stmt->bind_param('i', $id);
			//実行
			$stmt->execute();
			//コミットをする
			$this->mysqli->commit();
			print("削除しました<br>");
		} catch(Exception $e) {
			//ロールバックする
			$this->mysqli->rollback();
			print("削除に失敗しました<br>");
		}
		// DBを閉じる
		$stmt->close();
	}	

	public function changeHistory($id) {
		$result = $this->mysqli->query("SELECT * FROM userinfo where id = ".$id."");
		if($result){
			//1行取り出し
			while($row = $result->fetch_object()){
				//エスケープして表示
				$id = htmlspecialchars($row->id);
				$name = htmlspecialchars($row->name);
				$message = htmlspecialchars($row->message);
				$created = htmlspecialchars($row->created);
				print("変更内容<br>");
				print("$id: $name : <br>$message <br>($created)<br>");
			}
		}
		// DBを閉じる
		$this->mysqli->close();
	}

	public function createHistory() {
		//userinfoテーブルから登録日付の降順でデータを取得
		$result = $this->mysqli->query("SELECT * FROM userinfo ORDER BY created DESC LIMIT 1");
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
		$this->mysqli->close();
	}
}
?>