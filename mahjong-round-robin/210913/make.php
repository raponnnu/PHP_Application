<?php
require('dbconnect.php');
session_start();


if(!empty($_POST)){
	$table_len = mb_strlen($_POST['table_id'], "UTF-8");
	$table_wdt = mb_strwidth($_POST['table_id'], "UTF-8");
	//エラー項目の確認
	if($_POST['table_id'] == ''){
		$error['table_id'] = 'blank';
	}
	if($table_len != $table_wdt){
		$error['table_id'] = '2bit';
	}
	if($_POST['title'] == ''){
		$error['title'] = 'blank';
	}
	if(mb_strlen($_POST['password'], "UTF-8") < 8 ){
		$error['password'] = 'short';
	}
	if($_POST['password'] == '' && $_POST['password_c'] == ''){
		$error['password'] = 'blank';
	}
	if($_POST['password'] <> $_POST['password_c'] ){
		$error['password'] = 'notmatch';
	}
	if(!is_numeric($_POST['first_score'])){
		$error['first_score'] = 'notnumeric';
	}else if($_POST['first_score'] <= 0 || $_POST['first_score'] > 1000){
		$error['first_score'] = 'value';
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
	if(mb_strlen($_POST['vpass'], "UTF-8") < 8  && $_POST['show'] == 'n'){
		$error['vpass'] = 'short';
	}
	if($_POST['vpass'] == '' && $_POST['vpass_c'] == '' && $_POST['show'] == 'n'){
		$error['vpass'] = 'blank';
	}
	if($_POST['vpass'] <> $_POST['vpass_c']  && $_POST['show'] == 'n'){
		$error['vpass'] = 'notmatch';
	}
  	//重複アカウントのチェック
  	if (empty($error)) {
    		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_table WHERE table_id=?');
    		$member->execute(array($_POST['table_id']));
    		$record = $member->fetch();
    		if ($record['cnt'] > 0) {
      			$error['table_id'] = 'duplicate';
    		}
  	}

	if(empty($error)) {
		//player名前設定
		for($i=1;$i<=8;$i++){
			if (empty($_POST['player'.(string)($i)])){
     				$_POST['player'.(string)($i)] = 'player'.($i);
			}
		}
		//player並べ替え
		for($i=2;$i<=8;$i++){
			$change = rand($i, 8);
			$player_store = $_POST['player'.(string)($i)];
			$_POST['player'.(string)($i)] = $_POST['player'.(string)($change)];
			$_POST['player'.(string)($change)] = $player_store;
		}
		if($_POST['show'] == 'y'){
			$_POST['vpass'] = '';
		}
		
		$statement = $db->prepare('INSERT INTO mrr_table SET 
		table_id = ? ,
		title = ? , 
		password = ? ,
		player1 = ? ,
		player2 = ? ,
		player3 = ? ,
		player4 = ? ,
		player5 = ? ,
		player6 = ? ,
		player7 = ? ,
		player8 = ? ,
		first_score = ? ,
		base_score = ? ,
		rank1_score = ? ,
		rank2_score= ? ,
		rank3_score = ? ,
		rank4_score = ? ,
		same_rank = ? ,
		view_statue = ? ,
		view_password = ? ,
		comment = ? ,
		created = NOW() ' );
		
		$statement->execute(array($_POST['table_id'],	
			$_POST['title'],
			sha1($_POST['password']),
			$_POST['player1'],
			$_POST['player2'],
			$_POST['player3'],
			$_POST['player4'],
			$_POST['player5'],
			$_POST['player6'],
			$_POST['player7'],
			$_POST['player8'],
			$_POST['first_score'],
			$_POST['base_score'],
			$_POST['rank1'],
			$_POST['rank2'],
			$_POST['rank3'],
			$_POST['rank4'],
			$_POST['same_rank'],
			$_POST['show'],
			sha1($_POST['vpass']),
			$_POST['comment']
		));
		$_SESSION['join']['table_id'] = $_POST['table_id'];
		$_SESSION['join']['show'] = 'y';
		header('Location: make-complete.php');
		exit();
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
<title>総当たり戦　対戦表作成</title>
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
<div id="wrapper">
<!-- ヘッダー始まり -->
<div id="header">
<h1>総当たり戦　対戦表作成</h1>
</div>
<!-- // header END -->
<div id="contents">
<!-- ここから本文記入 -->

<form action = "make.php" method="post"  onsubmit="return check()">
 <h2>■対戦表作成</h2>
 <dl>
 <dt>対戦表ID(半角16文字以内)</dt>
<dd><input type="text" name="table_id" size="15" maxlength="16" 
value="<?php echo htmlspecialchars($_POST['table_id'], ENT_QUOTES);  ?>" />
<?php if( $error['table_id'] == 'blank'){ ?>
<p class="error" >* 対戦表IDを入力してください。</p>
<?php } ?>
<?php if( $error['table_id'] == '2bit'){ ?>
<p class="error" >* 対戦表IDは半角で入力してください。</p>
<?php } ?>
<?php if( $error['table_id'] == 'duplicate'){ ?>
<p class="error" >* その対戦表IDは既に使用されています。</p>
<?php } ?>
</dd>
 <dt>対戦表タイトル</dt>
<dd><input type="text" name="title" size="50" maxlength="255"
value="<?php echo htmlspecialchars($_POST['title'], ENT_QUOTES);  ?>" />
<?php if( $error['title'] == 'blank'){ ?>
<p class="error" >* 対戦表タイトルを入力してください。</p>
<?php } ?></dd>
 <dt>編集パスワード(8~16文字)</dt>
<dd><input type="password" name="password" size=15" maxlength="16"
value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES);  ?>" /></dd>
 <dt>編集パスワード(確認)</dt>
<dd><input type="password" name="password_c" size=15" maxlength="16"
value="<?php echo htmlspecialchars($_POST['password_c'], ENT_QUOTES);  ?>" />
<?php if( $error['password'] == 'blank'){ ?>
<p class="error" >* パスワードを入力してください。</p>
<?php } ?>
<?php if( $error['password'] == 'notmatch'){ ?>
<p class="error" >* パスワードが一致しません。</p>
<?php } ?>
<?php if( $error['password'] == 'short'){ ?>
<p class="error" >* パスワードが短すぎます。</p>
<?php } ?>
</dd>

 <dt> プレイヤー名</dt>
<dd>player 1 
<input type="text" name="player1" id="pl1" size="30" maxlength = "32" 
   placeholder="player1"
  value="<?php echo htmlspecialchars($_POST['player1'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 2 
<input type="text" name="player2" id="pl2" size="30" maxlength = "32" 
   placeholder="player2"
  value="<?php echo htmlspecialchars($_POST['player2'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 3 
<input type="text" name="player3" id="pl3" size="30" maxlength = "32" 
   placeholder="player3"
  value="<?php echo htmlspecialchars($_POST['player3'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 4 
<input type="text" name="player4" id="pl4" size="30" maxlength = "32" 
   placeholder="player4"
  value="<?php echo htmlspecialchars($_POST['player4'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 5 
<input type="text" name="player5" id="pl5" size="30" maxlength = "32" 
   placeholder="player5"
  value="<?php echo htmlspecialchars($_POST['player5'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 6 
<input type="text" name="player6" id="pl6" size="30" maxlength = "32" 
   placeholder="player6"
  value="<?php echo htmlspecialchars($_POST['player6'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 7 
<input type="text" name="player7" id="pl7" size="30" maxlength = "32" 
   placeholder="player7"
  value="<?php echo htmlspecialchars($_POST['player7'], ENT_QUOTES);  ?>" />
</dd>
<dd>player 8 
<input type="text" name="player8" id="pl8" size="30" maxlength = "32" 
   placeholder="player8"
  value="<?php echo htmlspecialchars($_POST['player8'], ENT_QUOTES);  ?>" />
</dd>
<dt>プレイヤーの順番は対戦表作成時にランダムに変更されます。</dt>

 <dt> 点数計算</dt>
<dd> 持ち点：<input type="text" name="first_score" size="3" maxlength="4" style="text-align:right" 
  value="<?php if(!empty($_POST['first_score'])){
echo htmlspecialchars($_POST['first_score'], ENT_QUOTES);
}else{
echo '250';
}  ?>" />00 (最大100,000点) 
<?php if( $error['first_score'] == 'notnumeric'){ ?>
<p class="error" >* 数字で入力してください。</p>
<?php } ?>
<?php if( $error['first_score'] == 'value'){ ?>
<p class="error" >* 0～100,000で入力してください。</p>
<?php } ?></dd>
<dd> 基準点：<input type="text" name="base_score" size="3" maxlength="4" style="text-align:right" 
 value="<?php if(!empty($_POST['base_score'])){
echo htmlspecialchars($_POST['base_score'], ENT_QUOTES);
}else{
echo '300';
}  ?>" />00 (最大200,000点)
<?php if( $error['base_score'] == 'notnumeric'){ ?>
<p class="error" >* 数字で入力してください。</p>
<?php } ?>
<?php if( $error['base_score'] == 'value'){ ?>
<p class="error" >* 基準点～200,000で入力してください。</p>
<?php } ?>
</dd>
<dd> 順位点：<input type="text" name="rank1" size="3" maxlength="5" style="text-align:right"  
value="<?php if(!empty($_POST['rank1'])){
echo htmlspecialchars($_POST['rank1'], ENT_QUOTES);
}else{
echo '300';
}  ?>" />00-<input type="text" name="rank2" size="3" maxlength="5" style="text-align:right" 
 value="<?php if(!empty($_POST['rank2'])){
echo htmlspecialchars($_POST['rank2'], ENT_QUOTES);
}else{
echo '100';
}  ?>" />00-<input type="text" name="rank3" size="3" maxlength="5" style="text-align:right"
value="<?php if(!empty($_POST['rank3'])){
echo htmlspecialchars($_POST['rank3'], ENT_QUOTES);
}else{
echo '-100';
}  ?>" />00-<input type="text" name="rank4" size="3" maxlength="5" style="text-align:right"
 value="<?php if(!empty($_POST['rank4'])){
echo htmlspecialchars($_POST['rank4'], ENT_QUOTES);
}else{
echo '-300';
}  ?>" />00 (最大100,000点 ~ 最小-100,000点)
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
<dd>
同着処理：<dd> <input type="radio" id="same_k" name="same_rank" value="k"  <?php if(empty($_POST['same_rank'])){
echo 'checked';
}else if($_POST['same_rank'] == 'k'){
echo 'checked';
}  ?> />
  <label for="same_k">上家取り</label> 
   <input type="radio" id="same_s" name="same_rank" value="s"  <?php if($_POST['same_rank'] == 's'){
echo 'checked';
}  ?> />
  <label for="same_s">順位点分配</label></dd>
</dd>

 <dt> 公開設定</dt>
<dd> <input type="radio" id="show_yes" name="show" value="y" onclick="Disableload();" <?php if(empty($_POST['show'])){
echo 'checked';
}else if($_POST['show'] == 'y'){
echo 'checked';
}  ?> />
  <label for="show_yes">全員に公開する</label> 
   <input type="radio" id="show_no" name="show" value="n" onclick="Disableload();" <?php if($_POST['show'] == 'n'){
echo 'checked';
}  ?> />
  <label for="show_no">全員に公開しない</label></dd>
<dd><span id="vpass">閲覧パスワード(8~16文字)</span>：<input type="password" id="vp" name="vpass" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['vpass'], ENT_QUOTES);  ?>" /></dd>
<dd><span id="vpass">閲覧パスワード(確認)</span>：<input type="password" id="vp_c" name="vpass_c" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['vpass_c'], ENT_QUOTES);  ?>" />
<?php if( $error['vpass'] == 'blank'){ ?>
<p class="error" >* パスワードを入力してください。</p>
<?php } ?>
<?php if( $error['vpass'] == 'notmatch'){ ?>
<p class="error" >* パスワードが一致しません。</p>
<?php } ?>
<?php if( $error['vpass'] == 'short'){ ?>
<p class="error" >* パスワードが短すぎます。</p>
<?php } ?>
 <dt>コメント(上限500文字) ※このコメントは公開されます。</dt>
<dd><textarea name="comment" rows="10" cols="50" maxlength="500" placeholder="ご自由にお書きください。" ><?php echo htmlspecialchars($_POST['comment'], ENT_QUOTES); ?></textarea></dd>
 
 </dl>
<input type="submit" value="決定"/>
</form>
<a href="index.php">戻る</a>
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