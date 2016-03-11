<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>掲示板</title>
  </head>
  <body>
    <?php
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
        
          //mysqliクラスのオブジェクトを作成
          $mysqli = new mysqli('localhost', 'root', '', 'testbase');
          //エラーが発生したら
          if ($mysqli->connect_error){
              print("接続失敗：" . $mysqli->connect_error);
              exit();
          }

          //userinfoテーブルからidを取得し存在している時のみ処理する
          $res = $mysqli->query("SELECT id FROM userinfo where id = ".$id."");
          if($res){
            //1行取り出し
            while($row = $res->fetch_object()){
              //エスケープして表示
              $id2 = htmlspecialchars($row->id);
          }

          if(!empty($id2) && ($id == $id2)){
            //プリペアドステートメントを作成　ユーザ入力を使用する箇所は?にしておく
            $stmt = $mysqli->prepare("UPDATE userinfo set message = ? where id = ?");
            //?の位置に値を割り当てる
            $stmt->bind_param('si', $message, $id);
            //実行
            $stmt->execute();
        
            //userinfoテーブルから変更したデータを取得
            $result = $mysqli->query("SELECT * FROM userinfo where id = ".$id."");
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
          } else {
          print("該当するスレッドは削除されたか、存在しません</br>");
          }
          // DBを閉じる
          $mysqli->close();
        }
       }
      }
    ?>
  </body>
</html>
