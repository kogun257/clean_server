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

          <form enctype="multipart/form-data"  action="./index_upfile.php" method="POST">
            <input type="hidden" name="name" value="value" />
            アップロード: <input id="file-sample" name="upfile" type="file" />
            <div class="photo-form" id="file-preview"></div>
                              
            <script src="js/jquery-3.3.1.min.js"></script>
            <script src="js/post.js"></script>

            <input type="submit" value="ファイル送信" />
          </form>
        
        </body>
  </html>