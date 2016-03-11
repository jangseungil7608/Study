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
      
      if (!empty($_GET['page'])){
          $now_page = htmlspecialchars($_GET['page']);
          $max_page = htmlspecialchars($_GET['maxpage']);
      }
      
      if (!empty($_GET['id']) && !empty($_GET['mode'])){
          $id = htmlspecialchars($_GET['id']);
          $mode = htmlspecialchars($_GET['mode']);
            print($id.$mode);
          if (!empty($_GET['mode'])){
            print("削除途中");
              echo deleteUser($id);
          }
      }

      if(empty($now_page) || ($now_page < 2)){

        //mysqliクラスのオブジェクトを作成
        $mysqli = new mysqli('localhost', 'root', '', 'testbase');
        //エラーが発生したら
        if ($mysqli->connect_error){
            print("接続失敗：" . $mysqli->connect_error);
            exit();
        }
	//userinfoテーブル内の件数を取得
	$all_count = $mysqli->query("SELECT COUNT(*) cnt  FROM userinfo");
        if($all_count){
            while($cont = $all_count->fetch_object()){
                $cnt = htmlspecialchars($cont->cnt);
            }
        }

        // メソッド内使用変数
        $now_page = 1;
        $view_page = 10;
        $max_page = ceil($cnt/$view_page);

	//userinfoテーブルから登録日付の降順でデータを取得
	$result = $mysqli->query("SELECT * FROM userinfo ORDER BY created LIMIT 10");
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
        $mysqli->close();
        
        echo changePage($now_page,$max_page);
	echo '<a href="testlist.php?page='.($now_page+1).'&maxpage='.$max_page.'">次のページへ >></a>';

      } else if ($now_page == $max_page) {
        echo selectUser($now_page,$max_page);
        echo '<a href="testlist.php?page='.($now_page-1).'&maxpage='.$max_page.'"><< 前のページへ</a>';
        echo changePage($now_page,$max_page);

      } else {
        echo selectUser($now_page,$max_page);
        echo '<a href="testlist.php?page='.($now_page-1).'&maxpage='.$max_page.'"><< 前のページへ</a>';
        echo changePage($now_page,$max_page);
	echo '<a href="testlist.php?page='.($now_page+1).'&maxpage='.$max_page.'">次のページへ >></a>';
      }

    
        function deleteUser($id){
                //mysqliクラスのオブジェクトを作成
                $mysqli = new mysqli('localhost', 'root', '', 'testbase');
                //エラーが発生したら
                if ($mysqli->connect_error){
                    print("接続失敗：" . $mysqli->connect_error);
                    exit();
                }
        	//userinfoテーブル内の該当レコードを削除
        	$stmt = $mysqli->prepare("DELETE FROM userinfo WHERE id = ?");
                //?の位置に値を割り当てる
                $stmt->bind_param('i', $id);
                //実行
                $stmt->execute();
                // DBを閉じる
                $mysqli->close();
        }

        function selectUser($now_page,$max_page){
                //mysqliクラスのオブジェクトを作成
                $mysqli = new mysqli('localhost', 'root', '', 'testbase');
                //エラーが発生したら
                if ($mysqli->connect_error){
                    print("接続失敗：" . $mysqli->connect_error);
                    exit();
                }
        	//userinfoテーブルから登録日付の降順でデータを取得
                $offset = ($now_page * 10) - 10;
        	$result = $mysqli->query("SELECT * FROM userinfo ORDER BY created LIMIT 10 OFFSET ".$offset);
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
                $mysqli->close();
        }

        function changePage($now_page,$max_page){
                //↓表示最大数が１０未満でページ表示数が１(表示数１ならページングする必要がない）でなければ・・・
                if(($max_page <= 10) && ($max_page != 1)){
                echo '  ';//表示があまり乱れないように全角空スペース４個を入れておく
                for($i=1;$i<=$max_page;$i++){
                	echo '<a href="testlist.php?page='.$i.'&maxpage='.$max_page.'">['.$i.']</a> ';
                	}
                	
                //↓最大表示数が１０以上で現在のページが６未満なら・・・	
                }elseif(($max_page > 10) && ($now_page<6)){
                echo '  ';//表示があまり乱れないように全角空スペース４個を入れておく
                	for($a=1;$a<=10;$a++){
                		echo '<a href="testlist.php?page='.$a.'&maxpage='.$max_page.'">['.$a.']</a> ';
                	}
                	
                //↓最大表示数が１０以上かつ、現在のページが６以上かつ最終ページより５ページ以内にいなければ・・・	
                }elseif(($max_page > 10) && ($now_page>=6) && (($now_page+5) < $max_page)){
                	echo '<a href="testlist.php?page=1&maxpage='.$max_page.'">最初へ</a>';
                	for($a=1;$a<=5;$a++){
                	echo '<a href="testlist.php?page='.($now_page-5+$a).'&maxpage='.$max_page.'">['.($now_page-5+$a).']</a> ';
                	}
                	for($a=1;$a<=5;$a++){
                	echo '<a href="testlist.php?page='.($now_page+$a).'&maxpage='.$max_page.'">['.($now_page+$a).']</a> ';
                	}
                        echo '<a href="testlist.php?page='.$max_page.'&maxpage='.$max_page.'">最後へ</a>';
                
                //↓最大表示数が１０以上かつ、現在のページも6以上かつ、
                //↓現在のページが最終ページから５ページ以内にいる場合の処理
                }elseif(($max_page > 10) && ($now_page>=6) && (($now_page+5) >= $max_page)){
                	echo '<a href="testlist.php?page=1&maxpage='.$max_page.'">最初へ</a> ';
                	$b = $max_page-10;
                	while($b <= $max_page){
                	echo '<a href="testlist.php?page='.$b.'&maxpage='.$max_page.'">['.$b.']</a> ';
                	$b++;
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
