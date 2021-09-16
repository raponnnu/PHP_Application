<?php
require('dbconnect.php');
require('gameary.php');
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

    	$result = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_score WHERE table_id=? AND number = ?');
    	$result->execute(array($_SESSION['join']['edit_table_id'], $_GET['match']));
    	$record_s = $result->fetch();
	$store_data =$record_s['cnt'];
    	if ($record_s['cnt'] > 0) {
		$result = $db->prepare('SELECT * FROM mrr_score WHERE table_id=? AND number = ?');
    		$result->execute(array($_SESSION['join']['edit_table_id'], $_GET['match']));
    		$record_s = $result->fetch();
	}
}

if($_GET['match']<1 || $_GET['match']>14){
	header('Location: edit.php');
	exit();
}

if(!empty($_POST)){
	$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=?');
    	$member->execute(array($_SESSION['join']['edit_table_id']));
    	$record = $member->fetch();
	if(($_POST['score1']+$_POST['score2']+$_POST['score3']+$_POST['score4'])!=($record['first_score']*4)){
		$error['score'] = 'notequal';
	}
	if(!is_numeric($_POST['score1'])||!is_numeric($_POST['score2'])||!is_numeric($_POST['score3'])||!is_numeric($_POST['score4'])){
		$error['score'] = 'notnumeric';
	}

	if(empty($error)){
		if($store_data>0){
			$statement = $db->prepare('UPDATE mrr_score SET 
				player1_score = ? ,
				player2_score = ? ,
				player3_score = ? ,
				player4_score = ? ,
				place = ? ,
				url = ? 
				WHERE table_id = ? AND number = ?' );

			$statement->execute(array(
				$_POST['score1'],
				$_POST['score2'],
				$_POST['score3'],
				$_POST['score4'],
				$_POST['place'],
				$_POST['url'],
				$_SESSION['join']['edit_table_id'],
				$_GET['match']
			));
		}else{
			$statement = $db->prepare('INSERT INTO mrr_score SET
				table_id = ? ,
				number = ? ,
				player1_score = ? ,
				player2_score = ? ,
				player3_score = ? ,
				player4_score = ? ,
				place = ? ,
				url = ?,
				created = NOW() ' );

			$statement->execute(array(
				$_SESSION['join']['edit_table_id'],
				$_GET['match'],
				$_POST['score1'],
				$_POST['score2'],
				$_POST['score3'],
				$_POST['score4'],
				$_POST['place'],
				$_POST['url']
			));
		}

		header('Location: edit.php');
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

<form action = "editscore.php?match=<?php echo ($_GET['match']);?>" method="post"   onsubmit="return check()">
 <h2>■対戦結果入力</h2>
<table  align="left" border=1 >
<thead>
<tr height="40"><th align="center">プレイヤー名</th><th  align="center">最終持ち点</th></tr>
</thead>
<tbody>
<tr height="26"><td><?php echo htmlspecialchars($record["player".$game_ary[$_GET['match']-1][0]], ENT_QUOTES); ?></td><td><input type="text" name="score1" size="5" maxlength="5" style="text-align:right" value="<?php if($store_data==0){echo ($_POST['score1']);}else{echo ($record_s['player1_score']);} ?>" />00</td></tr>
<tr height="26"><td><?php echo htmlspecialchars($record["player".$game_ary[$_GET['match']-1][1]], ENT_QUOTES); ?></td><td><input type="text" name="score2" size="5" maxlength="5" style="text-align:right" value="<?php if($store_data==0){echo ($_POST['score2']);}else{echo ($record_s['player2_score']);} ?>" />00</td></tr>
<tr height="26"><td><?php echo htmlspecialchars($record["player".$game_ary[$_GET['match']-1][2]], ENT_QUOTES); ?></td><td><input type="text" name="score3" size="5" maxlength="5" style="text-align:right" value="<?php if($store_data==0){echo ($_POST['score3']);}else{echo ($record_s['player3_score']);} ?>" />00</td></tr>
<tr height="26"><td><?php echo htmlspecialchars($record["player".$game_ary[$_GET['match']-1][3]], ENT_QUOTES); ?></td><td><input type="text" name="score4" size="5" maxlength="5" style="text-align:right" value="<?php if($store_data==0){echo ($_POST['score4']);}else{echo ($record_s['player4_score']);} ?>" />00</td></tr>
</tbody>
</table>
<table border=1>
<thead>
<tr height="40"><th  align="center">席順</th></tr>
</thead>
<tbody id="sortable">
<tr height="26" id="<?php if(empty($_POST)){ if($store_data==0){echo ("E");}else{echo (substr($record_s['place'],0,1));}}else{echo (substr($_POST['place'],0,1));} ?>"><td style="text-align:center" ><?php if(empty($_POST)){ if($store_data==0){echo ("東");}else{echo ($places[(substr($record_s['place'],0,1))]);}}else{echo ($places[(substr($_POST['place'],0,1))]);} ?></td></tr>
<tr height="26" id="<?php if(empty($_POST)){ if($store_data==0){echo ("S");}else{echo (substr($record_s['place'],2,1));}}else{echo (substr($_POST['place'],2,1));} ?>"><td style="text-align:center"><?php if(empty($_POST)){ if($store_data==0){echo ("南");}else{echo ($places[(substr($record_s['place'],2,1))]);}}else{echo ($places[(substr($_POST['place'],2,1))]);} ?></td></tr>
<tr height="26" id="<?php if(empty($_POST)){ if($store_data==0){echo ("W");}else{echo (substr($record_s['place'],4,1));}}else{echo (substr($_POST['place'],4,1));} ?>"><td style="text-align:center"><?php if(empty($_POST)){ if($store_data==0){echo ("西");}else{echo ($places[(substr($record_s['place'],4,1))]);}}else{echo ($places[(substr($_POST['place'],4,1))]);} ?></td></tr>
<tr height="26" id="<?php if(empty($_POST)){ if($store_data==0){echo ("N");}else{echo (substr($record_s['place'],6,1));}}else{echo (substr($_POST['place'],6,1));} ?>"><td style="text-align:center"><?php if(empty($_POST)){ if($store_data==0){echo ("北");}else{echo ($places[(substr($record_s['place'],6,1))]);}}else{echo ($places[(substr($_POST['place'],6,1))]);} ?></td></tr>
</tbody>
</table>
<br>
<?php if( $error['score'] == 'notequal'){ ?>
<p class="error" >* 点数の合計値が正しくありません。</p>
<?php } ?>
<?php if( $error['score'] == 'notnumeric'){ ?>
<p class="error" >* 最終持ち点は数字で入力してください。</p>
<?php } ?>


<dl>
	<dt>リンクURL（牌符などご自由にお使いください。）</dt>
	<dd><input type="text" name="url" size="80" maxlength="500" value="<?php if($store_data>0){echo ($record_s['url']);} ?>"/></dd>
</dl>
<input type="hidden" name="place" id="plc" value="<?php if(empty($_POST)){ if($store_data==0){echo ("E,S,W,N");}else{echo ($record_s['place']);}}else{echo($_POST['place']);} ?>"></p>
<input type="submit" value="決定"/>
</form>
<br>
<a href="edit.php">戻る</a>

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
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script> 
    <script type="text/javascript" src="move.js"></script>
    <script>
   $(function() {
   // リストを並べ替え可能に
   $('#sortable').sortable({
     // updateで並べ替えるたびに更新
     update: function(){
       // toArrayで現在の順番を取得し出力
       $("#log").text($('#sortable').sortable("toArray"));
	document.getElementById("plc").value = $('#sortable').sortable("toArray");
     }
   });
 });
</script>
</body>
</html>