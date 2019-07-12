<?php
//データベースへのアクセス
  $dsn='データベース名';
	$user='ユーザ名';
	$password='パスワード';
	$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//データベース内にテーブルを作成する(投稿番号：id,名前：name,コメント：comment,投稿日時：date,パスワード：pass)
  $sql = "CREATE TABLE IF NOT EXISTS missiontest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date char(64),"
	. "pass char(32)"
	.");";
	$stmt = $pdo->query($sql);

//valueの初期設定
  $data1="";
  $data2="";
  $data3="";
//フォームが空のときは動かないようにする
  if(isset($_POST['edit']) or isset($_POST['name']) or isset($_POST['delete'])): //1
//ボタン内容の受信
  $formtype=$_POST['formtype'];
//入力フォームへの分岐
  	if($formtype=="送信" && isset($_POST['name']) && isset($_POST['comment']) && isset($_POST['pass1'])){ //2
	//新規投稿への分岐
		if(empty($_POST['himitsu'])){ //3
	//入力フォームから送信された内容をPHPで受け取る
		$name=$_POST['name'];
		$comment=$_POST['comment'];
	//日時を表す関数を用意する
		$Date= date("Y/n/j h:i:s");
	//パスワードの取得
		$pass1=$_POST['pass1'];

	//INSERTによるデータベースへの書き込み
	$sql = $pdo -> prepare("INSERT INTO missiontest (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $Date, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass1, PDO::PARAM_STR);
	$sql -> execute();

	//編集済み投稿への分岐
		}else{ //a
	//秘密の受信
		$himin=$_POST['himitsu'];
	//入力フォームから送信された内容をPHPで受け取る
		$name=$_POST['name'];
		$comment=$_POST['comment'];
	//日時を表す関数を用意する
		$Date= date("Y/n/j h:i:s");
	//パスワードの取得
		$pass1=$_POST['pass1'];

	//DBから元パスワードの取得
		$id = $himin;
		$sql = 'SELECT pass FROM missiontest where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$motopass = $stmt->fetch();

		if($motopass['pass'] == $pass1){

	//UPDATEによる投稿内容の編集
		$id = $himin; //変更する投稿番号
		$sql = 'update missiontest set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->bindParam(':date', $Date, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $pass1, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		}
		}
//削除フォームへの分岐 //a
	}elseif($formtype=="削除" && isset($_POST['delete'])){
//削除フォームから送信された削除対象番号をPHPで受け取る
  	$dn=$_POST['delete'];
//パスワードの取得
	$pass2=$_POST['pass2'];

//DBから元パスワードの取得
	$id = $dn;
	$sql = 'SELECT pass FROM missiontest where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$motopass = $stmt->fetch();
	if($motopass['pass'] == $pass2){
//DELETEによる投稿内容の削除
	$id = $dn;
	$sql = 'delete from missiontest where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	}
//編集フォームへの分岐
	}elseif($formtype=="編集" && isset($_POST['edit'])){ //9
//編集番号の受信
	$editnum=$_POST['edit'];
//パスワードの取得
	$pass3=$_POST['pass3'];

//DBから元パスワードの取得
	$id = $editnum;
	$sql = 'SELECT pass FROM missiontest where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$motopass = $stmt->fetch();

	if($motopass['pass'] == $pass3){
//SELECTによる名前の取得
	$id = $editnum;
	$sql = 'SELECT name FROM missiontest where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$editname = $stmt->fetch();

//SELECTによるコメントの取得
	$id = $editnum;
	$sql = 'SELECT comment FROM missiontest where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$editcomment = $stmt->fetch();

//編集対象の投稿をフォームに表示
	$data1=$editname['name'];
	$data2=$editcomment['comment'];
	$data3=$editnum;
	} //9
	}
  endif; //1
?>
<html>
<p>この掲示板のテーマ</p><h1>「今ハマっている曲」</h1>
<!-「名前」「コメント」の入力と送信ボタンで構成された入力フォームをfromタグで作る->
<!- 「編集番号指定用フォーム」を用意：「編集対象番号」の入力と編集ボタン ->
<body>
<meta charset="utf-8">
<form method="POST" action="mission_5-2.php" name="send">
	名前：<input type="text" name="name" value="<?php echo $data1; ?>"/><br>
        コメント：<input type="text" name="comment" value="<?php echo $data2; ?>"/><br>
	パスワード：<input type="text" name="pass1" value=""/><br>
        <input type="submit" name="formtype" value="送信"><br>
	<input type="hidden" name="himitsu" value="<?php echo $data3; ?>">	
</form>
<form method="POST" action="mission_5-2.php" name="sakuzyo">
        削除対象番号：<input type="text" name="delete" value=""/><br>
	パスワード：<input type="text" name="pass2" value=""/><br>
        <input type="submit" name="formtype" value="削除"><br>
</form>
<form method="POST" action="mission_5-2.php" name="hensyuu">
        編集対象番号：<input type="text" name="edit" value=""/><br>
	パスワード：<input type="text" name="pass3" value=""/><br>
        <input type="submit" name="formtype" value="編集"><br>
</form>
<p>
<?php
  if(isset($_POST['edit']) or isset($_POST['name']) or isset($_POST['delete'])){

//入力したデータをselectにより表示する
  $sql = 'SELECT * FROM missiontest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	echo "<hr>";
	}
  }
?>
</p>
</body>
</html>
