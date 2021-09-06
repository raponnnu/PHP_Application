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

<form action = editdata.php" method="post">
 <h2>■対戦表作成</h2>
 <dl>
 <dt>対戦表ID</dt>
<dd>table_id:id(変更不可)</dd>
 <dt>タイトル</dt>
<dd><input type="text" name="title" size="15" maxlength="255" /></dd>
 <dt>編集パスワード</dt>
<dd>********<a href="editpass".php">(変更する)</a></dd>


 <dt> プレイヤー名</dt>
<dd>player 1 
<input type="text" name="player1" id="pl1" size="30" maxlength = "32" 
   placeholder="player1 " value="" >
</dd>
<dd>player 2 
<input type="text" name="player2" id="pl2" size="30" maxlength = "32" 
   placeholder="player2 " value="" >
</dd>
<dd>player 3 
<input type="text" name="player3" id="pl3" size="30" maxlength = "32" 
   placeholder="player3 " value="" >
</dd>
<dd>player 4 
<input type="text" name="player4" id="pl4" size="30" maxlength = "32" 
   placeholder="player4 " value="" >
</dd>
<dd>player 5 
<input type="text" name="player5" id="pl5" size="30" maxlength = "32" 
   placeholder="player5 " value="" >
</dd>
<dd>player 6 
<input type="text" name="player6" id="pl6" size="30" maxlength = "32" 
   placeholder="player6 " value="" >
</dd>
<dd>player 7 
<input type="text" name="player7" id="pl7" size="30" maxlength = "32" 
   placeholder="player7 " value="" >
</dd>
<dd>player 8 
<input type="text" name="player8" id="pl8" size="30" maxlength = "32" 
   placeholder="player8 " value="" >
</dd>


 <dt> 点数計算</dt>
<dd> 持ち点：25000点(変更不可)</dd>
<dd> 基準点：<input type="text" name="base_score" size="3" maxlength="4" style="text-align:right" value="300" />00 (最大200,000点)</dd>
<dd> 順位点：<input type="text" name="rank1" size="3" maxlength="5" style="text-align:right" value="300" />00-<input type="text" name="rank2" size="3" maxlength="5" style="text-align:right" value="100" />00-<input type="text" name="rank3" size="3" maxlength="5" style="text-align:right" value="-100" />00-<input type="text" name="rank4" size="3" maxlength="5" style="text-align:right" value="-300" />00 (最大100,000点 ~ 最小-100,000点)</dd>

 <dt> 公開設定 <a href="editvpass".php">変更</a></dt>
 <dt>コメント(上限500文字) ※このコメントは公開されます。</dt>
<dd><textarea name="comment" rows="10" cols="50" maxlength="500" placeholder="ご自由にお書きください。"></textarea></dd>
 
 </dl>
<input type="submit" value="決定"/>
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