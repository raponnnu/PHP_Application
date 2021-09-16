<?php
require('dbconnect.php');
require('gameary.php');
session_start();


if(empty($_GET['table_id'])){
	header('Location: index.php');
	exit();
}else if($_SESSION['join']['show']== "n" ){
	header('Location: preview.php');
	exit();
}else{
    	$member = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_table WHERE table_id=?');
    	$member->execute(array($_REQUEST['table_id']));
    	$record = $member->fetch();
    	if ($record['cnt'] == 0) {
		$_SESSION['nothing'] = 1;
		header('Location: index.php');
		exit();
    	}else{	
		$member = $db->prepare('SELECT * FROM mrr_table WHERE table_id=?');
    		$member->execute(array($_GET['table_id']));
    		$record = $member->fetch();
		$_SESSION['join']['table_id'] = $_GET['table_id'];
	}
}


$rank_score=[];
$rank_score[]=$record['rank1_score']+4*($record['base_score']-$record['first_score']);
for($i=1;$i<4;$i++){
	$rank_score[]=$record['rank'.($i+1).'_score'];
}

$scores = [];
$urls = [];
for($i=1;$i<=7;$i++){
	for ($j=0;$j<=1;$j++){
    		$result = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_score WHERE table_id=? AND number = ?');
    		$result->execute(array($_SESSION['join']['table_id'], (($i-1)*2+$j+1)));
    		$record_s = $result->fetch();
    		if ($record_s['cnt'] > 0) {
			$result = $db->prepare('SELECT * FROM mrr_score WHERE table_id=? AND number = ?');
    			$result->execute(array($_SESSION['join']['table_id'], (($i-1)*2+$j+1)));
    			$record_s = $result->fetch();
			
			$urls[($i-1)*2+$j] = $record_s['url'];
			$scores[] = [];
			for ($k=0;$k<=3;$k++){
				$scores[($i-1)*2+$j][] = [
					'player' => $record["player".$game_ary[($i-1)*2+$j][$k]],
					'score' => $record_s["player".($k+1)."_score"],
					'place' => substr($record_s['place'],($k*2),1),
					'place_n' => $places_n[substr($record_s['place'],($k*2),1)],
					'f_score' => 0
				];
			}
			// ソートする
  			$arrScore = [];
  			$arrPlace = [];
			foreach ($scores[($i-1)*2+$j] as $pl => $detail) {
  				$arrScore[] = $detail['score'];
  				$arrPlace[] = $detail['place_n'];
			}
			

			array_multisort($arrScore, SORT_DESC, SORT_NUMERIC, $arrPlace, SORT_ASC, SORT_NUMERIC, $scores[($i-1)*2+$j]);
			//同着時の処理
			for ($k=0;$k<=3;$k++){
				if($record['same_rank']=='k'){
					$scores[($i-1)*2+$j][$k]['f_score'] = (float)($scores[($i-1)*2+$j][$k]['score']-$record['base_score']+$rank_score[$k])/10;
				}else{
					$f_rank = 0;
					$f_cnt = 0;
					for($l=0;$l<=3;$l++){
						if($scores[($i-1)*2+$j][$l]['score'] == $scores[($i-1)*2+$j][$k]['score']){
							$f_rank += $rank_score[$l];
							$f_cnt++;
						}
					}
					$f_rank = (float)$f_rank/$f_cnt;
					$scores[($i-1)*2+$j][$k]['f_score'] = (float)($scores[($i-1)*2+$j][$k]['score']-$record['base_score']+$f_rank)/10;
				}
			}
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

 <h2><?php echo htmlspecialchars($record['title'], ENT_QUOTES); ?></h2>
<a href="preedit.php">編集する</a>

 <h3> 点数計算ルール</h3>
<dl>
<dd> 持ち点：<?php echo($record['first_score']); ?>00点</dd>
<dd> 基準点：<?php echo($record['base_score']); ?>00点</dd>
<dd> 順位点：<?php if($record['rank1_score'] < 0){echo ('▲');} echo(abs((float)$record['rank1_score']/10)); ?> - <?php if($record['rank2_score'] < 0){echo ('▲');} echo(abs((float)$record['rank2_score']/10)); ?> - <?php if($record['rank3_score'] < 0){echo ('▲');} echo(abs((float)$record['rank3_score']/10)); ?> - <?php if($record['rank4_score'] < 0){echo ('▲');} echo(abs((float)$record['rank4_score']/10)); ?></dd>
<dd> 同着処理：<?php if($record['same_rank'] == 'k'){echo ('上家取り');}else{echo ('順位点分配');}  ?></dd>
</dl>
<h3>コメント</h3>
<dl><dd><?php echo nl2br(htmlspecialchars($record['comment'], ENT_QUOTES)); ?></dd></dl>

<h3>順位</h3>
<table border=1>
<tr><th align="center">順位</th><th align="center">プレイヤー名</th><th align="center">スコア</th></tr>
<?php
$total_scores = [];
for($i=0;$i<8;$i++){
	$total_scores[] = [
		'player' => $record["player".($i+1)],
		'score' => 0
	];
}
for($i=0;$i<7;$i++){
	for ($j=0;$j<=1;$j++){
		for ($k=0;$k<4;$k++){
			for ($l=0;$l<4;$l++){
				if($total_scores[$game_ary[$i*2+$j][$k]-1]['player'] == $scores[$i*2+$j][$l]['player']){
					$total_scores[$game_ary[$i*2+$j][$k]-1]['score'] += $scores[$i*2+$j][$l]['f_score'];
				}
			}
		}
	}
}

// ソートする
$arrScore = [];
foreach ($total_scores as $pl => $detail) {
	{
  		$arrScore[] = $detail['score'];
	}
}
array_multisort($arrScore, SORT_DESC, SORT_NUMERIC,  $total_scores);

for($i=0;$i<8;$i++){
	echo ("<tr><th align=\"center\">");
	$same = 0;
	for($j=$i;$j>0;$j--){
		if($total_scores[$i]['score'] == $total_scores[$j-1]['score']){
			$same++;
		}else{
			break;
		}
	}
	echo ($i+1-$same);
	echo ("位</th><td>");
	echo ($total_scores[$i]['player']);
	echo ("</td><td align=\"right\">");
	if($total_scores[$i]['score'] < 0){echo ('▲');}
	echo (abs($total_scores[$i]['score']));
	echo ("pt</td></tr>\n");
}
?>
</table>

<h3>スコア</h3>
<table border=1>
<tr><th align="center">プレイヤー名</th><th align="center">1試合目</th><th align="center">2試合目</th><th align="center">3試合目</th><th align="center">4試合目</th><th align="center">5試合目</th><th align="center">6試合目</th><th align="center">7試合目</th><th align="center">スコア</th></tr>
<?php
for($i=0;$i<8;$i++){
	echo ("<tr><th>");
	echo ($record["player".($i+1)]);
	echo ("</th>");
	for($j=0;$j<7;$j++){
		echo ("<td  align=\"right\" ");
		for($k=0;$k<=1;$k++){
			for($l=0;$l<4;$l++){
				if($game_ary[$j*2+$k][$l] == ($i+1)){
					if($k==0){
						echo ("class=\"Atable\" ");
					}else{
						echo ("class=\"Btable\" ");
					}
				}
			}
		}
		echo (">");
		for($k=0;$k<=1;$k++){
			for($l=0;$l<4;$l++){
				if($scores[$j*2+$k][$l]['player'] == $record["player".($i+1)]){
					if($scores[$j*2+$k][$l]['f_score'] < 0){echo ('▲');}
					echo(abs($scores[$j*2+$k][$l]['f_score'])."pt");
				}else{
					echo(" ");
				}
			}
		}
		echo ("</td>");
	}
	echo ("<th align=\"right\">");
	for($j=0;$j<8;$j++){
		if($record["player".($i+1)] == $total_scores[$j]['player']){
			if($total_scores[$j]['score'] < 0){echo ('▲');}
			echo (abs($total_scores[$j]['score']));
		}
	}
	echo ("pt</th></tr>\n");
}
?>
</table>

<h3>対戦リスト</h3>
<?php
for($i=1;$i<=7;$i++){
	echo ("<h4>" . $i . "試合目</h4>\n");
	for ($j=0;$j<=1;$j++){
		echo ("<p>");
		if($j==0){
			echo("A");
		}else{
			echo("B");
		}
		echo ("卓<p>\n");
    		$result = $db->prepare('SELECT COUNT(*) AS cnt FROM mrr_score WHERE table_id=? AND number = ?');
    		$result->execute(array($_SESSION['join']['table_id'], (($i-1)*2+$j+1)));
    		$record_s = $result->fetch();
    		if ($record_s['cnt'] > 0) {
			echo ("<table border=1 >\n<tr><th align=\"center\">着順</th><th align=\"center\">プレイヤー名</th><th align=\"center\">スコア</th><th align=\"center\">最終持ち点</th><th align=\"center\">席順</th></tr>");
				for ($k=0;$k<=3;$k++){
				echo ("<tr><td  align=\"center\">");
				if($record['same_rank']=='k'){
					echo ($k+1);
				}else{
					$same = 0;
					for($l=$k;$l>0;$l--){
						if($scores[($i-1)*2+$j][$k]['score'] == $scores[($i-1)*2+$j][$l-1]['score'] ){
							$same++;
						}else{
							break;
						}
					}
					echo ($k+1-$same);
				}
				echo ("</td><td>");
				echo htmlspecialchars($scores[($i-1)*2+$j][$k]['player']);
				echo ("</td><td align=\"right\">");
				if($scores[($i-1)*2+$j][$k]['f_score'] < 0){echo ('▲');}
				echo (sprintf("%.1f",abs($scores[($i-1)*2+$j][$k]['f_score'])));
				echo ("pt</td><td align=\"right\">");
				echo (number_format($scores[($i-1)*2+$j][$k]['score']*100));
				echo ("</td><td align=\"center\">");
				echo ($places[$scores[($i-1)*2+$j][$k]['place']]);
				echo ("</td></tr>\n");
			}
			echo ("</table>\n");			
			if($urls[($i-1)*2+$j]<>""){
				echo ("<a href=\"".  $urls[($i-1)*2+$j] ."\">[リンク]</a>\n");
			}
			

		}else{
			echo ("<p>");
			for ($k=0;$k<=3;$k++){
				echo htmlspecialchars($record["player".$game_ary[($i-1)*2+$j][$k]], ENT_QUOTES);
				if($k<3){
					echo (" - ");
				}
			}
			echo ("</p>\n");
		}
		echo ("<br>");
	}
} 
?>




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
    <script type="text/javascript" src="move.js"></script>
</body>
</html>