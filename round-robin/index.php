<?php
session_start();

if(isset($_POST['reset'])) {
	session_destroy();
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
<form action="./" method="post">
	<input type="submit" value="リセット" name="reset">
</form>
<form action = "result.php" method="post">
 <h2>■人数(3~20人)</h2>
 <select name="people" id="selectPeople">
<?php for ($i=3; $i<=20; $i++){ ?>
<option value = "<?php echo($i);?>" <?php  if((int)$_SESSION['people']==$i){echo("selected");}?> > <?php  print($i); ?>人 </option>
<?php } ?>
 </select>

 <h2>■プレイヤー名</h2>
<?php for ($i=1; $i<=20; $i++){ ?>
<p><?php printf('player %02d',$i); ?> 
<input type="text" name="player<?php echo($i);?>" id="pl<?php echo($i);?>" size="16" maxlength = "16" 
   placeholder="player<?php print($i); ?> " value="<?php echo($_SESSION['player'.(string)($i)]); ?>" >
</p>
<?php } ?>

 <div><input type="submit" value="対戦表作成" /></div>
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
    <script type="text/javascript" src="move.js"></script>
</body>
</html>