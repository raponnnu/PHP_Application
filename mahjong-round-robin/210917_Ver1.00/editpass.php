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
}

if(!empty($_POST)){
	//エラー項目の確認
	if(mb_strlen($_POST['password_now'], "UTF-8")<8 || mb_strlen($_POST['password'], "UTF-8")<8 || mb_strlen($_POST['password_c'], "UTF-8")<8 ){
		$error['password'] = 'short';
	}
	if($_POST['password_now'] == '' || $_POST['password'] == '' || $_POST['password_c'] == '' ){
		$error['password'] = 'blank';
	}
	if($_POST['password'] <> $_POST['password_c'] ){
		$error['password'] = 'notmatch2';
	}
  	//アカウントのチェック
  	if (empty($error)) {
    		$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=? AND password = ?');
    		$member->execute(array($_SESSION['join']['edit_table_id'], sha1($_POST['password_now'])));
    		$record = $member->fetch();
    		if ($record) {
			$statement = $db->prepare('UPDATE mrr_table SET 
				password = ? 
				WHERE table_id = ?' );
		
			$statement->execute(array(
				sha1($_POST['password']),
				$_SESSION['join']['edit_table_id']
			));

			header('Location: edit-complete.php');
			exit();
    		}else{
			$error['password'] = 'notmatch';
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

<form action = "editpass.php" method="post" onsubmit="return check()">
 <h2>■編集パスワード更新</h2>
 <dl>
<dt>id：<?php echo($_SESSION['join']['edit_table_id']); ?></dt>
 <dt>現在のパスワード</dt>
<dd><input type="password" name="password_now" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['password_now'], ENT_QUOTES);  ?>" /></dd>
 <dt>新しいパスワード</dt>
<dd><input type="password" name="password" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES);  ?>" /></dd>
 <dt>新しいパスワード(確認)</dt>
<dd><input type="password" name="password_c" size=15" maxlength="16" value="<?php echo htmlspecialchars($_POST['password_c'], ENT_QUOTES);  ?>" /></dd>
<dd>
<?php if( $error['password'] == 'blank'){ ?>
<p class="error" >* パスワードを入力してください。</p>
<?php } ?>
<?php if( $error['password'] == 'short'){ ?>
<p class="error" >* パスワードが短すぎます。</p>
<?php } ?>
<?php if( $error['password'] == 'notmatch'){ ?>
<p class="error" >* 現在のパスワードが一致しません。</p>
<?php } ?>
<?php if( $error['password'] == 'notmatch2'){ ?>
<p class="error" >* 新しいパスワードが一致しません。</p>
<?php } ?>
</dd>
 </dl>
<input type="submit" value="決定"/>
</form>
<br>
<a href="editdata.php">戻る</a>

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