<?php
require('dbconnect.php');
session_start();

if(empty($_SESSION['join']['edit_table_id'])){
	header('Location: view.php?table_id=' . $_SESSION['join']['table_id']);
	exit();
}else if($_SESSION['time'] + 3600 < time() ){
	unset($_SESSION['join']['edit_table_id']);
	header('Location: view.php?table_id=' . $_SESSION['join']['table_id']);
	exit();
}else{
	$_SESSION['time'] = time();

	$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=?');
    	$member->execute(array($_SESSION['join']['edit_table_id']));
    	$record = $member->fetch();
	if(!empty($_POST)){
		//エラー項目の確認
		if($_POST['title'] == ''){
			$error['title'] = 'blank';
		}
		for($i=1;$i<=8;$i++){
			if (empty($_POST['player'.(string)($i)])){
				$error['player'] = 'blank';
			}
		}
		if(!is_numeric($_POST['base_score'])){
			$error['base_score'] = 'notnumeric';
		}else if($_POST['base_score'] < $_POST['first_score'] || $_POST['base_score'] > 2000){
			$error['base_score'] = 'value';
		}
		if(!is_numeric($_POST['rank1'])||!is_numeric($_POST['rank2'])||!is_numeric($_POST['rank3'])||!is_numeric($_POST['rank4'])){
			$error['rank'] = 'notnumeric';
		}else if($_POST['rank1'] < -1000 || $_POST['rank1'] > 1000 || $_POST['rank2'] < -1000 || $_POST['rank2'] > 1000 || $_POST['rank3'] < -1000 || $_POST['rank3'] > 1000 || $_POST['rank4'] < -1000 || $_POST['rank4'] > 1000 ){
			$error['rank'] = 'value';
		}
		if(($_POST['rank1']+$_POST['rank2']+$_POST['rank3']+$_POST['rank4'])!=0){
			$error['rank'] = 'notequal';
		}
		if($_POST['rank1']<$_POST['rank2']||$_POST['rank2']<$_POST['rank3']||$_POST['rank3']<$_POST['rank4']){
			$error['rank'] = 'notasc';
		}

		if(empty($error)) {
		
			$statement = $db->prepare('UPDATE mrr_table SET 
				title = ? , 
				player1 = ? ,
				player2 = ? ,
				player3 = ? ,
				player4 = ? ,
				player5 = ? ,
				player6 = ? ,
				player7 = ? ,
				player8 = ? ,
				base_score = ? ,
				rank1_score = ? ,
				rank2_score= ? ,
				rank3_score = ? ,
				rank4_score = ? ,
				same_rank = ? ,
				comment = ?  
				WHERE table_id = ?' );
		
			$statement->execute(array(
				$_POST['title'],
				$_POST['player1'],
				$_POST['player2'],
				$_POST['player3'],
				$_POST['player4'],
				$_POST['player5'],
				$_POST['player6'],
				$_POST['player7'],
				$_POST['player8'],
				$_POST['base_score'],
				$_POST['rank1'],
				$_POST['rank2'],
				$_POST['rank3'],
				$_POST['rank4'],
				$_POST['same_rank'],
				$_POST['comment'],
				$_SESSION['join']['edit_table_id']
			));

			header('Location: edit-complete.php');
			exit();
		}
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="description" content="">
<meta name="keywords" content="">
<title>麻雀 ８人総当たり戦対戦表ツール</title>
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
<div id="wrapper">
<!-- ヘッダー始まり -->
<div id="header">
<h1>麻雀 ８人総当たり戦対戦表</h1>
</div>
<!-- // header END -->
<div id="contents">
<!-- ここから本文記入 -->

<form action = "editdata.php" method="post" onsubmit="return check()">
 <h2>■対戦表作成</h2>
 <dl>
 <dt>対戦表ID</dt>
<dd><?php echo($record['table_id']); ?>(変更不可)</dd>
 <dt>タイトル</dt>
<dd><input type="text" name="title" size="50" maxlength="255" value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['title'], ENT_QUOTES); }else{echo htmlspecialchars($record['title'], ENT_QUOTES);} ?>"/>
<?php if( $error['title'] == 'blank'){ ?>
<p class="error" >* 対戦表タイトルを入力してください。</p>
<?php } ?></dd>
 <dt>編集パスワード</dt>
<dd>********<a href="editpass".php">(変更する)</a></dd>


 <dt> プレイヤー名</dt>
<dd>player 1 
<input type="text" name="player1" id="pl1" size="30" maxlength = "32" 
   placeholder="player1 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player1'], ENT_QUOTES); }else{echo htmlspecialchars($record['player1'], ENT_QUOTES); }?>" >
</dd>
<dd>player 2 
<input type="text" name="player2" id="pl2" size="30" maxlength = "32" 
   placeholder="player2 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player2'], ENT_QUOTES); }else{echo htmlspecialchars($record['player2'], ENT_QUOTES); }?>" >
</dd>
<dd>player 3 
<input type="text" name="player3" id="pl3" size="30" maxlength = "32" 
   placeholder="player3 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player3'], ENT_QUOTES); }else{echo htmlspecialchars($record['player3'], ENT_QUOTES); }?>" >
</dd>
<dd>player 4 
<input type="text" name="player4" id="pl4" size="30" maxlength = "32" 
   placeholder="player4 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player4'], ENT_QUOTES); }else{echo htmlspecialchars($record['player4'], ENT_QUOTES); }?>" >
</dd>
<dd>player 5 
<input type="text" name="player5" id="pl5" size="30" maxlength = "32" 
   placeholder="player5 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player5'], ENT_QUOTES); }else{echo htmlspecialchars($record['player5'], ENT_QUOTES); }?>" >
</dd>
<dd>player 6 
<input type="text" name="player6" id="pl6" size="30" maxlength = "32" 
   placeholder="player6 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player6'], ENT_QUOTES); }else{echo htmlspecialchars($record['player6'], ENT_QUOTES); }?>" >
</dd>
<dd>player 7 
<input type="text" name="player7" id="pl7" size="30" maxlength = "32" 
   placeholder="player7 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player7'], ENT_QUOTES); }else{echo htmlspecialchars($record['player7'], ENT_QUOTES); }?>" >
</dd>
<dd>player 8 
<input type="text" name="player8" id="pl8" size="30" maxlength = "32" 
   placeholder="player8 " value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['player8'], ENT_QUOTES); }else{echo htmlspecialchars($record['player8'], ENT_QUOTES); }?>" >
</dd>
<dd>
<?php if( $error['player'] == 'blank'){ ?>
<p class="error" >* プレイヤー名をすべて入力してください。</p>
<?php } ?></dd>


 <dt> 点数計算</dt>
<dd> 持ち点：<?php echo($record['first_score']); ?>00点(変更不可)</dd>
<dd> 基準点：<input type="text" name="base_score" size="3" maxlength="4" style="text-align:right" value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['base_score'], ENT_QUOTES); }else{echo htmlspecialchars($record['base_score'], ENT_QUOTES); }?>" />00 (最大200,000点)
<?php if( $error['base_score'] == 'notnumeric'){ ?>
<p class="error" >* 数字で入力してください。</p>
<?php } ?>
<?php if( $error['base_score'] == 'value'){ ?>
<p class="error" >* 基準点～200,000で入力してください。</p>
<?php } ?></dd>
<dd> 順位点：<input type="text" name="rank1" size="3" maxlength="5" style="text-align:right" value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['rank1'], ENT_QUOTES); }else{echo htmlspecialchars($record['rank1_score'], ENT_QUOTES); }?>" />00-<input type="text" name="rank2" size="3" maxlength="5" style="text-align:right" value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['rank2'], ENT_QUOTES); }else{echo htmlspecialchars($record['rank2_score'], ENT_QUOTES); }?>" />00-<input type="text" name="rank3" size="3" maxlength="5" style="text-align:right" value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['rank3'], ENT_QUOTES); }else{echo htmlspecialchars($record['rank3_score'], ENT_QUOTES); }?>" />00-<input type="text" name="rank4" size="3" maxlength="5" style="text-align:right" value="<?php if (!empty($_POST)){echo htmlspecialchars($_POST['rank4'], ENT_QUOTES); }else{echo htmlspecialchars($record['rank4_score'], ENT_QUOTES); }?>" />00 (最大100,000点 ~ 最小-100,000点)</dd>
<?php if( $error['rank'] == 'notnumeric'){ ?>
<p class="error" >* 数字で入力してください。</p>
<?php } ?>
<?php if( $error['rank'] == 'value'){ ?>
<p class="error" >* -100,000～100,000で入力してください。</p>
<?php } ?>
<?php if( $error['rank'] == 'notequal'){ ?>
<p class="error" >* 順位点の総和を0にしてください。</p>
<?php } ?>
<?php if( $error['rank'] == 'notasc'){ ?>
<p class="error" >* 順位点は1位≧2位≧3位≧4位としてください。</p>
<?php } ?>
</dd>
同着処理： <input type="radio" id="same_k" name="same_rank" value="k"  <?php 
if (!empty($_POST)){ 
	if($_POST['same_rank'] == 'k'){
		echo 'checked';
	}
}else{
	 if($record['same_rank'] == 'k'){
		echo 'checked';
	}
}  ?> />
  <label for="same_k">上家取り</label> 
   <input type="radio" id="same_s" name="same_rank" value="s"  <?php 
if (!empty($_POST)){ 
	if($_POST['same_rank'] == 's'){
		echo 'checked';
	}
}else{
	 if($record['same_rank'] == 's'){
		echo 'checked';
	}
}  ?> />
  <label for="same_s">順位点分配</label>
</dd>
 <dt> 公開設定 <a href="editvpass".php">(変更する)</a></dt>
 <dt>コメント(上限500文字) ※このコメントは公開されます。</dt>
<dd><textarea name="comment" rows="10" cols="50" maxlength="500" placeholder="ご自由にお書きください。"><?php
if (!empty($_POST)){
	echo htmlspecialchars($_POST['comment'], ENT_QUOTES);
 }else{
	echo htmlspecialchars($record['comment'], ENT_QUOTES); 
}?></textarea></dd>
 
 </dl>
<input type="submit" value="更新"/>
</form>
<br>
<a href="edit.php">点数入力選択画面に戻る</a>
<br><br><br>
<a href="delete.php">この対戦表を削除する</a>
<!-- // 本文ココまで*-->
</div>
<!-- // contents END -->
<div id="footer">
<!-- 著作権表記部分 -->
<p>Copyright &copy; rapolister.work. All Rights Reserved.</p>
<!-- 
著作権表記 スタイルシート(CSS)レイアウト
以下の部分は削除しないで下さい。 CSSによる大きさ、色の変更は可能です。
-->
<p id="csslink">Template <a href="http://css.rakugan.com/" target="_blank">スタイルシート(CSS)レイアウト</a></p>
</div>
<!-- // footer END -->
</div>
<!-- // wrapper END -->

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="move.js"></script>
</body>
</html>