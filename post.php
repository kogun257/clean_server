<?php
/*それぞれの変数に初期値入力*/
$datetime = '';
$subject = '';
$comment = '';

 ?>

<!DOCTYPE>
<html lang="ja">
        <head>
          <meta charset="UTF-8">
	      <title>データ投げ込みシステム(テスト)</title>
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <link rel="stylesheet" type="text/css" href="css/reset.css">
          <link rel="stylesheet" type="text/css" href="css/input.css">
          <link rel="stylesheet" type="text/css" href="css/responsive_post.css">
        </head>
        <body>

          <p class="head-form">入力フォーム</p>

	        <div class="input_area">
	           <form action="./post2.php" method="post" enctype="multipart/form-data" id="contact_form">
	                <div class="item">
	                        <label class="label">写真</label>
                            <input id="file-sample" type="file" name="upfile" required>
                            <div class="photo-form" id="file-preview"></div>
	                </div>
                  
                  <script src="js/jquery-3.3.1.min.js"></script>
                  <script src="js/post.js"></script>

	                <div class="btn-area">
						        <input type="submit" value="次へ">
			            </div>
	           </form>
	        </div>
        </body>
</html>
