<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8" />
	<title>掲示板</title>
  </head>
  <body>
	<font size="6" color="#003300"><b>練習掲示板</b></font></br>
	</br>
	<?php
	require_once('mySqlObj.php');
	//mysqlObjのインスタンス生成
	$mySqlObj = new mySqlObj();
	//初期ページ以外の場合
	if (!empty($_GET['page'])){
		$now_page = htmlspecialchars($_GET['page']);
		$max_page = htmlspecialchars($_GET['maxpage']);
	}
	//削除リングが押下された場合
	if (!empty($_GET['id']) && !empty($_GET['mode'])){
		$id = htmlspecialchars($_GET['id']);
		$mode = htmlspecialchars($_GET['mode']);
	  	if (!empty($_GET['mode'])){
			$mySqlObj->deleteUser($id);
		}
	}
	//初期ページ時
	if(empty($now_page) || ($now_page < 2)){
		//総件数を取得
		$cnt = $mySqlObj->selectUserCount();
		// メソッド内使用変数
		$now_page = 1;
		$view_page = 10;
		$max_page = ceil($cnt/$view_page);
		//表示
		$mySqlObj->selectBulletinBoard($now_page,$max_page);
		echo changePage($now_page,$max_page);
		echo '<a href="testlist.php?page='.($now_page+1).'&maxpage='.$max_page.'">次のページへ >></a>';
	//中間ページ時
	} else if ($now_page == $max_page) {
		//表示
		$mySqlObj->selectBulletinBoardNext($now_page,$max_page);
		//echo selectUser($now_page,$max_page,$mysqli);
		echo '<a href="testlist.php?page='.($now_page-1).'&maxpage='.$max_page.'"><< 前のページへ</a>';
		echo changePage($now_page,$max_page);
	//最終ページ時
	} else {
		//表示
		$mySqlObj->selectBulletinBoardNext($now_page,$max_page);
		//echo selectUser($now_page,$max_page,$mysqli);
		echo '<a href="testlist.php?page='.($now_page-1).'&maxpage='.$max_page.'"><< 前のページへ</a>';
		echo changePage($now_page,$max_page);
		echo '<a href="testlist.php?page='.($now_page+1).'&maxpage='.$max_page.'">次のページへ >></a>';
	}

function changePage($now_page,$max_page){
	//↓表示最大数が１０未満でページ表示数が１以外
	if(($max_page <= 10) && ($max_page != 1)){
		echo '  ';//表示があまり乱れないように全角空スペースを入れる
	for($index=1;$index<=$max_page;$index++){
		echo '<a href="testlist.php?page='.$index.'&maxpage='.$max_page.'">['.$index.']</a> ';
	}
		
	//↓最大表示数が１０以上で現在のページが６未満	
	}elseif(($max_page > 10) && ($now_page<6)){
		echo '  ';//表示があまり乱れないように全角空スペースを入れる
		for($index=1;$index<=10;$index++){
			echo '<a href="testlist.php?page='.$index.'&maxpage='.$max_page.'">['.$index.']</a> ';
		}
	//↓最大表示数が１０以上かつ、現在のページが６以上かつ最終ページより５ページ以内にいない	
	}else{
		if(($max_page > 10) && ($now_page>=6) && (($now_page+5) < $max_page)){
			echo '<a href="testlist.php?page=1&maxpage='.$max_page.'">最初へ</a>';
			for($index=1;$index<=5;$index++){
				echo '<a href="testlist.php?page='.($now_page-5+$index).'&maxpage='.$max_page.'">['.($now_page-5+$index).']</a> ';
			}
			for($index=1;$index<=5;$index++){
				echo '<a href="testlist.php?page='.($now_page+$index).'&maxpage='.$max_page.'">['.($now_page+$index).']</a> ';
			}
			echo '<a href="testlist.php?page='.$max_page.'&maxpage='.$max_page.'">最後へ</a>';			
		//↓最大表示数が１０以上かつ、現在のページも6以上かつ、
		//↓現在のページが最終ページから５ページ以内にいる場合の処理
		}else{
			echo '<a href="testlist.php?page=1&maxpage='.$max_page.'">最初へ</a> ';
			$index = $max_page-10;
			while($index <= $max_page){
				echo '<a href="testlist.php?page='.$index.'&maxpage='.$max_page.'">['.$index.']</a> ';
				$index++;
			}
		}
	}
}
	?>
</br>
</br>
</br>
	<a href="./post.php">新規投稿</a>
  </body>
</html>
