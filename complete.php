<?php
    session_start();
?>

<!DOCTYPE>
<html lang="ja">
        <head>
          <meta charset="UTF-8">
	      <title>データ投げ込みシステム(テスト)</title>
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <link rel="stylesheet" type="text/css" href="css/reset.css">
          <link rel="stylesheet" type="text/css" href="css/input.css">
          <link rel="stylesheet" type="text/css" href="css/responsive_complete.css">
        </head>
        <body>

        <p class="head-form">入力が完了しました</p>

	      <div class="input_area">
	        <form action="./index2.php" method="post" id="contact_form">

          <p><?php
              $var = "入力ありがとうございました!";
              echo $var;
           ?></p>

          <div class="btn-area">
            <input type="submit" name="btn_complete" value="入力ページに戻る">
          </div>

          </form>
         </div>
       </body>
</html>

<?php
	//セッション終了
	$_SESSION = array();
	//var_dump($_SESSION);    //array(0) { }が表示されればOK
?>
