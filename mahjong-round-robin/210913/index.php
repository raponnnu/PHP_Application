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
  	//アカウントのチェック
  	if (empty($error)) {
    		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_table WHERE table_id=?');
    		$member->execute(array($_POST['table_id']));
    		$record = $member->fetch();
    		if ($record['cnt'] > 0) {
    			$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=?');
    			$member->execute(array($_POST['table_id']));
    			$record = $member->fetch();
			$_SESSION['join']['pre_table_id'] = $_POST['table_id'];
			$_SESSION['join']['show'] = $record['view_statue'];
			$_SESSION['time'] == time();
			header('Location: view.php?table_id=' . $_POST['table_id']);
			exit();
    		}else{
			$error['table_id'] = 'nothing';
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

<form action = "index.php" method="post">
<?php if( $_SESSION['nothing'] == '1'){ ?>
<p class="error" >* 入力した対戦表IDは存在しません。</p>
<?php 
unset($_SESSION['nothing']);
} 
?>
 <h2>■対戦表ID検索</h2>
 <p><input type="text" name="table_id" size="35" maxlength="16" /> <input type="submit" value="決定"/></p>
<?php if( $error['table_id'] == 'blank'){ ?>
<p class="error" >* 対戦表IDを入力してください。</p>
<?php } ?>
<?php if( $error['table_id'] == '2bit'){ ?>
<p class="error" >* 対戦表IDは半角で入力してください。</p>
<?php } ?>
<?php if( $error['table_id'] == 'nothing'){ ?>
<p class="error" >* その対戦表IDは存在しません。</p>
<?php } ?>
<a href="make.php">新しい対戦表を作成</a>
</form>


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