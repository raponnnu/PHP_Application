<?php
if(!empty($_POST)){
	session_start();
	$_SESSION = $_POST;
	$pl_cnt = (int)$_POST['people'] ;
	$players = [];
	for ($i=0; $i< $pl_cnt ; $i++){ 
		if ($_POST['player'.(string)($i+1)] == ''){
     			$players[] = 'player'.($i+1);
 		}else{
 			$players[] = $_POST['player'.(string)($i+1)]; 
		} 
	}
	
	//名前並べ替え
	for ($i=0; $i< $pl_cnt; $i++){
		$change = rand($i, ($pl_cnt-1));
		$player_store = $players[$i];
		$players[$i] = $players[$change];
		$players[$change] = $player_store;
	}
}else{
	header('Location: http://rapostudy.lomo.jp/work/round-robin/');
	exit();
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
<title>総当たり戦　対戦表</title>
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
<div id="wrapper">
<!-- ヘッダー始まり -->
<div id="header">
<h1>総当たり戦　対戦表</h1>
</div>
<!-- // header END -->
<div id="contents">
<!-- ここから本文記入 -->

 <h2>■ プレイヤーリスト</h2>
<p>
<?php for ($i=0; $i< $pl_cnt ; $i++){ ?>
<?php 
 print($players[$i]);
?> 
</p>
<?php } ?>
 <p>( 人数 <?php echo($pl_cnt); ?>人 )</p>

 <h2>■ 対戦リスト</h2>
<?php 
$players_match = $players;
if(($pl_cnt)%2==0){
	$matchs = $pl_cnt-1;
}else{
	$matchs = $pl_cnt;
}
//対戦カードテーブル作成
$matchlists = [];
for ($i=0; $i< $pl_cnt ; $i++){ 
	$matchlists[] = [];
	for ($j=0; $j< $pl_cnt ; $j++){ 
		$matchlists[$j][] = "休み";
	}
}
for ($i=0; $i< $matchs ; $i++){ ?>
<p>第 <?php echo($i+1); ?> 試合&emsp;
<?php 



       //対戦カード出力
if(($pl_cnt)%2==0){
	for ($j=0; $j< ($pl_cnt)/2; $j++){
		if($j==0){
			print($players_match[0]."-".$players_match[$pl_cnt/2]);
			$matchlists[0][$i] = $players_match[$pl_cnt/2];
			for($k=0;$k<$pl_cnt;$k++){
				if($players_match[$pl_cnt/2] == $players[$k]){
					$matchlists[$k][$i] = $players_match[0];
				}
			}
			
		}else{
			print($players_match[($pl_cnt/2)-$j]."-".$players_match[($pl_cnt/2)+$j]);
			for($k=0;$k<$pl_cnt;$k++){
				if($players_match[($pl_cnt/2)-$j] == $players[$k]){
					$matchlists[$k][$i] = $players_match[($pl_cnt/2)+$j];
				}
			}
			for($k=0;$k<$pl_cnt;$k++){
				if($players_match[($pl_cnt/2)+$j] == $players[$k]){
					$matchlists[$k][$i] = $players_match[($pl_cnt/2)-$j];
				}
			}
		}
		echo("&emsp;");
	}
}else{
	for ($j=0; $j< ($pl_cnt-1)/2; $j++){
		print($players_match[(($pl_cnt-1)/2)-1-$j]."-".$players_match[(($pl_cnt-1)/2)+1+$j]);
		for($k=0;$k<$pl_cnt;$k++){
			if($players_match[(($pl_cnt-1)/2)-1-$j] == $players[$k]){
				$matchlists[$k][$i] = $players_match[(($pl_cnt-1)/2)+1+$j];
			}
		}
		for($k=0;$k<$pl_cnt;$k++){
			if($players_match[(($pl_cnt-1)/2)+1+$j] == $players[$k]){
				$matchlists[$k][$i] = $players_match[(($pl_cnt-1)/2)-1-$j];
			}
		}
		echo("&emsp;");
	}
	print("休み：".$players_match[(($pl_cnt-1)/2)]);
	echo("&emsp;");
}
?> 
</p>
<?php 
       //対戦カード並べ替え
	if(($pl_cnt)%2==0){
		$odd=1;
	}else{
		$odd=0;
	}
	$player_store = $players_match[$odd];
	for ($k=$odd; $k< ($pl_cnt-1); $k++){
		$players_match[$k] = $players_match[$k+1];
	}
	$players_match[($pl_cnt-1)] = $player_store;
} ?>
 <p>( 1人当たり <?php echo($pl_cnt-1); ?>試合 )</p>

 <h2>■ 対戦リスト（プレイヤー別）</h2>
<table border=1>
<?php 
echo ("<tr><th></th>");
$players_match = $players;
for ($i=0; $i< $pl_cnt-$odd ; $i++){ 
	echo("<th>");
	print(($i+1) . "試合目");
	echo("</th>");
 } 

//対戦カードテーブル出力
for ($i=0; $i< $pl_cnt; $i++){
	echo ("<tr><th>");
	print($players[$i]);
	echo("</th>");
	for($j=0; $j< $pl_cnt-$odd; $j++){
		echo("<td>");
		print($matchlists[$i][$j]);
		echo("</td>");
	}
	echo("</tr>");
}?>
</table>

 <h2>■ 対戦リスト（テーブル）</h2>
<table border=1>
<?php 
echo ("<tr><th></th>");
for ($i=0; $i< $pl_cnt ; $i++){ 
	echo("<th>");
	print($players[$i]);
	echo("</th>");
 } 
echo ("</tr>");


//対戦カードテーブル出力
for ($i=0; $i< $pl_cnt; $i++){
	echo ("<tr><th>");
	print($players[$i]);
	echo("</th>");
	for($j=0; $j< $pl_cnt; $j++){
		echo("<td>");
		for($k=0; $k< $pl_cnt; $k++){
			if($matchlists[$j][$k] == $players[$i] ){
				print($k+1);
			}
		}
		echo("</td>");
	}
	echo("</tr>");
}?>
</table>
<a href="./">プレイヤー設定修正</a>
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