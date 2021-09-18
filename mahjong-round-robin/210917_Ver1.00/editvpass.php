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
}

if(!empty($_POST)){
	//エラー項目の確認
	if(mb_strlen($_POST['vpass'], "UTF-8")<8 || mb_strlen($_POST['vpass_c'], "UTF-8")<8 ){
		$error['vpass'] = 'short';
	}
	if($_POST['vpass'] == '' || $_POST['vpass_c'] == '' ){
		$error['vpass'] = 'blank';
	}
	if($_POST['vpass'] <> $_POST['vpass_c'] ){
		$error['vpass'] = 'notmatch';
	}
  	//アカウントのチェック
  	if (empty($error) || ($_POST['show'] == 'y' && empty($error['vpass_now']))) {
		if($_POST['show'] == 'y'){
			$_POST['vpass'] = '';
			$$error['vpass'] = '';
		}
    		$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=? AND view_password = ?');
    		$member->execute(array($_SESSION['join']['edit_table_id'], sha1($_POST['vpass_now'])));
    		$record = $member->fetch();
    		if ($record) {
			$statement = $db->prepare('UPDATE mrr_table SET 
				view_statue = ?,
				view_password = ?
				WHERE table_id = ?' );
		
			$statement->execute(array(
				$_POST['show'],
				sha1($_POST['vpass']),
				$_SESSION['join']['edit_table_id']
			));

			header('Location: edit-complete.php');
			exit();
    		}else{
			$error['vpass_now'] = 'notmatch';
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

<form action = "editvpass.php" method="post" onsubmit="return check()">
 <h2>■現在の閲覧パスワード入力</h2>
<dl>
 <dt>現在のパスワード</dt>
<dd><input type="password" name="vpass_now" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['vpass_now'], ENT_QUOTES);  ?>" <?php if($record['view_statue']=='y'){
echo 'disabled';
} ?> /></dd>
<dd>
<?php if( $error['vpass_now'] == 'notmatch'){ ?>
<p class="error" >* パスワードが一致しません。</p>
<?php } ?>
</dd>
</dl>
 <h2>■公開設定</h2>
 <dl>
<dd>
<input type="radio" id="show_yes" name="show" value="y" onclick="Disableload();" <?php if(empty($_POST) && $record['view_statue']=='y'){
echo 'checked';
}else if($_POST['show'] == 'y'){
echo 'checked';
}  ?> />
  <label for="show_yes">全員に公開する</label> 
   <input type="radio" id="show_no" name="show" value="n" onclick="Disableload();" <?php if(empty($_POST) && $record['view_statue']=='n'){
echo 'checked';
} if($_POST['show'] == 'n'){
echo 'checked';
}  ?> />
  <label for="show_no">全員に公開しない</label>
</dd>
</dl>
 <h2>■閲覧パスワード更新</h2>
<dl>
 <dt>新しいパスワード</dt>
<dd><input type="password" id="vp" name="vpass" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['vpass'], ENT_QUOTES);  ?>"/></dd>
 <dt>新しいパスワード(確認)</dt>
<dd><input type="password" id="vp_c" name="vpass_c" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['vpass_c'], ENT_QUOTES);  ?>"/></dd>
<dd>
<?php if( $error['vpass'] == 'blank'){ ?>
<p class="error" >* パスワードを入力してください。</p>
<?php } ?>
<?php if( $error['vpass'] == 'short'){ ?>
<p class="error" >* パスワードが短すぎます。</p>
<?php } ?>
<?php if( $error['vpass'] == 'notmatch'){ ?>
<p class="error" >* 新しいパスワードが一致しません。</p>
<?php } ?>
</dd>
 </dl>
<input type="submit" value="更新"/>
</form>
<br>
<a href="editdata.php">対戦表設定画面に戻る</a>
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