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
    	$member = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_table WHERE table_id=?');
    	$member->execute(array($_SESSION['join']['edit_table_id']));
    	$record = $member->fetch();
    	if ($record['cnt'] == 0) {
		header('Location: index.php');
		exit();
    	}
}

if(!empty($_POST)){
  	//アカウントのチェック
    	$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=? AND password = ?');
    	$member->execute(array($_SESSION['join']['edit_table_id'], sha1($_POST['password'])));
    	$record = $member->fetch();
    	if ($record) {
		$statement = $db->prepare('DELETE FROM mrr_table WHERE table_id = ?' );
			$statement->execute(array(
				$_SESSION['join']['edit_table_id'],
			));
		header('Location: delete-complete.php');
		exit();
    	}else{
		$error['password'] = 'notmatch';
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

<form action = "delete.php" method="post" onsubmit="return check()">
<h3>対戦表を削除します。</h3>
<p>パスワードを入力してください。</p>
<p>table_id：<?php echo($_SESSION['join']['edit_table_id']); ?></p>
 <p><input type="password" name="password" size="35" maxlength="16" /> <input type="submit" value="決定"/></p>
</form>
<?php if( $error['password'] == 'notmatch'){ ?>
<p class="error" >* パスワードが一致しません。</p>
<?php } ?>
</form>
 <p><a href="editdata.php" >戻る</a>

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