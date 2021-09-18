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

<h2><?php echo htmlspecialchars($record['title'], ENT_QUOTES); ?></h2>
<a href="editdata.php">対戦表設定を変更する</a>


 <h3> 点数計算ルール</h3>
<dl>
<dd> 持ち点：<?php echo($record['first_score']); ?>00点</dd>
<dd> 基準点：<?php echo($record['base_score']); ?>00点</dd>
<dd> 順位点：<?php if($record['rank1_score'] < 0){echo ('▲');} echo(abs((float)$record['rank1_score']/10)); ?> - <?php if($record['rank2_score'] < 0){echo ('▲');} echo(abs((float)$record['rank2_score']/10)); ?> - <?php if($record['rank3_score'] < 0){echo ('▲');} echo(abs((float)$record['rank3_score']/10)); ?> - <?php if($record['rank4_score'] < 0){echo ('▲');} echo(abs((float)$record['rank4_score']/10)); ?></dd>
<dd> 同着処理：<?php if($record['same_rank'] == 'k'){echo ('上家取り');}else{echo ('順位点分配');}  ?></dd>
</dl>
<h3>コメント</h3>
<dl><dd><?php echo nl2br(htmlspecialchars($record['comment'], ENT_QUOTES)); ?></dd></dl>


<h3>対戦結果更新</h3>
<?php
$rank_score=[];
$rank_score[]=$record['rank1_score']+4*($record['base_score']-$record['first_score']);
for($i=1;$i<4;$i++){
	$rank_score[]=$record['rank'.($i+1).'_score'];
}
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
    		$result->execute(array($_SESSION['join']['edit_table_id'], (($i-1)*2+$j+1)));
    		$record_s = $result->fetch();
    		if ($record_s['cnt'] > 0) {
			$result = $db->prepare('SELECT * FROM mrr_score WHERE table_id=? AND number = ?');
    			$result->execute(array($_SESSION['join']['edit_table_id'], (($i-1)*2+$j+1)));
    			$record_s = $result->fetch();
			echo ("<table border=1 >\n<tr><th align=\"center\">着順</th><th align=\"center\">プレイヤー名</th><th align=\"center\">スコア</th><th align=\"center\">最終持ち点</th><th align=\"center\">席順</th></tr>");
			$scores = [];
			for ($k=0;$k<=3;$k++){
				$scores[] = [
					'player' => $record["player".$game_ary[($i-1)*2+$j][$k]],
					'score' => $record_s["player".($k+1)."_score"],
					'place' => substr($record_s['place'],($k*2),1),
					'place_n' => $places_n[substr($record_s['place'],($k*2),1)]
				];
			}
  			$arrScore = [];
  			$arrPlace = [];
			foreach ($scores as $pl => $detail) {
  				$arrScore[] = $detail['score'];
  				$arrPlace[] = $detail['place_n'];
			}
			

			// ソートする
			array_multisort($arrScore, SORT_DESC, SORT_NUMERIC, $arrPlace, SORT_ASC, SORT_NUMERIC, $scores);
			for ($k=0;$k<=3;$k++){
				echo ("<tr><td  align=\"center\">");
				if($record['same_rank']=='k'){
					echo ($k+1);
				}else{
					$same = 0;
					for($l=$k;$l>0;$l--){
						if($scores[$k]['score'] == $scores[$l-1]['score'] ){
							$same++;
						}else{
							break;
						}
					}
					echo ($k+1-$same);
				}
				echo ("</td><td>");
				echo htmlspecialchars($scores[$k]['player']);
				echo ("</td><td align=\"right\">");
				if($record['same_rank']=='k'){
					$f_score = (float)($scores[$k]['score']-$record['base_score']+$rank_score[$k])/10;
				}else{
					$f_rank = 0;
					$f_cnt = 0;
					for($l=0;$l<=3;$l++){
						if($scores[$l]['score'] == $scores[$k]['score']){
							$f_rank += $rank_score[$l];
							$f_cnt++;
						}
					}
					$f_rank = (float)$f_rank/$f_cnt;
					$f_score = (float)($scores[$k]['score']-$record['base_score']+$f_rank)/10;
				}
				if($f_score < 0){echo ('▲');}
				echo (sprintf("%.1f",abs($f_score)));
				echo ("</td><td align=\"right\">");
				echo (number_format($scores[$k]['score']*100));
				echo ("</td><td align=\"center\">");
				echo ($places[$scores[$k]['place']]);
				echo ("</td></tr>\n");
			}
			echo ("</table>\n");
			if($record_s['url'] <> ""){
				echo ("<a href=\"".  $record_s['url'] ."\" target=\"_blank\">[リンク]</a>\n");
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
		echo("<p><a href=\"editscore.php?match=". (string)(($i-1)*2+$j+1) ."\">[更新]</a></p>\n");
	}
} ?>


 <p><a href="view.php?table_id=<?php echo($_SESSION['join']['table_id']) ?>" >閲覧画面に戻る</a>

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