<?php
require('dbconnect.php');
session_start();

if($_SESSION['join']['show']== "y" ){
	$_SESSION['join']['table_id'] = $_SESSION['join']['pre_table_id'];
	header('Location: view.php?table_id=' . $_SESSION['join']['table_id']);
	exit();
}

if(!empty($_POST)){
	//エラー項目の確認
	if(mb_strlen($_POST['vpass'], "UTF-8")<8){
		$error['vpass'] = 'short';
	}
	if($_POST['vpass'] == ''){
		$error['vpass'] = 'blank';
	}
  	//アカウントのチェック
  	if (empty($error)) {
		//echo ($_SESSION['join']['pre_table_id']. " " .sha1($_POST['vpass']));
    		$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=? AND view_password = ?');
    		$member->execute(array($_SESSION['join']['pre_table_id'], sha1($_POST['vpass'])));
    		$record = $member->fetch();
    		if ($record) {
			$_SESSION['join']['table_id'] = $_SESSION['join']['pre_table_id'];
			$_SESSION['join']['show'] = "y" ;
			header('Location: view.php?table_id=' . $_SESSION['join']['table_id']);
			exit();
    		}else{
			$error['vpass'] = 'notmatch';
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

<form action = "preview.php" method="post">
<p>table_id：<?php echo($_SESSION['join']['pre_table_id']); ?></p>
<p>※この対戦表は閲覧パスワードが必要です。閲覧パスワードを入力してください。</p>
 <p><input type="password" name="vpass" size="35" maxlength="16" /> <input type="submit" value="決定"/></p>
<?php if( $error['vpass'] == 'blank'){ ?>
<p class="error" >* パスワードを入力してください。</p>
<?php } ?>
<?php if( $error['vpass'] == 'notmatch'){ ?>
<p class="error" >* パスワードが一致しません。</p>
<?php } ?>
<?php if( $error['vpass'] == 'short'){ ?>
<p class="error" >* パスワードが短すぎます。</p>
<?php } ?>
</form>
<a href="index.php" >戻る</a>

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
</body>
</html>