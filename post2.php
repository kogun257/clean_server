<?php
//phpの処理部分長くね？

/*それぞれの変数に初期値入力*/
$datetime = '';
$subject = '';
$comment = '';

//参考元：3分で覚え直す$_SESSIONの使い方まとめ [PHP]　ブクマ位置:php
//参考元：PHPで 「Webページの有効期限が切れてます」となる時の傾向と対策　ブクマ位置:php

//データ受け渡し用にsession開始
session_start([
        'cookie_lifetime' => 600,	//セッションの有効期限を10分に設定
]);
header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

# ファイル名を調べる
echo "image_title:";
echo $_FILES['upfile']['name']. "<br>";

# ファイルサイズを調べる用
echo '$_FILES[\'upfile\'][\'size\']:';
echo $_FILES['upfile']['size'] . '<br>';

# MIMEタイプを調べる用
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['upfile']['tmp_name']);
echo 'mime:';
echo $mime . '<br>';

# MIMEから画像か動画かを判断
if(preg_match("/image/",$mime)){
    echo "mime is one of image.<br>";
}elseif(preg_match("/video/",$mime)){
    echo "mime is one of video.<br>";
}

echo "<br><br>($_FILES):";
var_dump($_FILES);
echo "<br><br>";

//アップロードファイルのチェックと保存のため
try {

    echo "isset:";
    echo isset($_FILES['upfile']['error']) ? "データが入っています。<br />" : "データなし<br />";

    echo "is_int:";
    echo is_int($_FILES['upfile']['error']) ? "整数型です。" : "データなし<br />";

	// 未定義である・複数ファイルである・$_FILES Corruption 攻撃を受けた
	/* どれかに該当していれば不正なパラメータとして処理する
	if (!isset($_FILES['upfile']['error']) || !is_int($_FILES['upfile']['error'])) {
		throw new RuntimeException('パラメータが不正です');
	}*/

	// $_FILES['upfile']['error'] の値を確認
	switch ($_FILES['upfile']['error']) {
		case UPLOAD_ERR_OK: // OK
			break;
			# throw new RuntimeException('UPLOAD_ERR_OK');
		case UPLOAD_ERR_NO_FILE:   // ファイル未選択
			throw new RuntimeException('画像ファイルが選択されていません');
		case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
		case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 最大256MB->php.ini   ファイルサイズは2分の動画を想定
			throw new RuntimeException('ファイルサイズが大きすぎます');
		default:
			throw new RuntimeException('その他のエラーが発生しました');
	}

	// ここで定義するサイズ上限のオーバーチェック
	// (必要がある場合のみ)
	//if ($_FILES['upfile']['size'] > 5000000) {
	//	throw new RuntimeException('ファイルサイズが大きすぎます');
    //}
    
	
	// $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので
	// MIMEタイプに対応する拡張子を自前で取得する
	$finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['upfile']['tmp_name']);
	if (!$ext = array_search(
        $mime,
	    array(
		    'gif' => 'image/gif',
		    'jpg' => 'image/jpeg',
		    'png' => 'image/png',

		    'mp4' => 'video/mp4',
		    'mov' => 'video/quicktime'
	    ),
	    true
	)) {
	    throw new RuntimeException('ファイル形式が不正です<br />');
	}

} catch (RuntimeException $e) {

	exit($e->getMessage());
}

//画像ファイルの有無を確認後、セッションにデータを格納
if(isset($_FILES['upfile'])){
    $image_title = $_FILES['upfile']['name'];
	$image_tmp = $_FILES['upfile']['tmp_name'];

	if(preg_match("/image/",$mime)){
		$image_data = file_get_contents($_FILES['upfile']['tmp_name']);

	}elseif(preg_match("/video/",$mime)){
		try {
			//ファイル名を年月日に変更して、ファイル名の重複を避けかつ検索性を向上させる
			$up_date = '_' . date("Ymd");
	
			// ファイルデータからS
			$image_data = file_get_contents($_FILES['upfile']['tmp_name']);
			// アップロードされたファイルに、パスとファイル名を設定して保存
			if (!file_put_contents(
				$image_path = sprintf('tmp/t_img/%s%s.%s',	# 動画はフォルダに保存してから表示する
				sha1($image_tmp),
				$up_date,
				$ext
				),
				$image_data,
				LOCK_SH
				)
			){
				throw new RuntimeException('ファイル保存時にエラーが発生しました');
			}
		} catch (RuntimeException $e) {
			exit($e->getMessage());
		}
	}

	//データ受け渡し
	$_SESSION['image_title'] = $image_title;
	$_SESSION['image_data'] = $image_data;
	$_SESSION['image_tmp'] = $image_tmp;
	$_SESSION['image_ext'] = $ext;

	//echo 'セッションにデータ入れたで';
}else{echo 'セッション失敗。<br />';}

	// ライブラリを読み込む
	require_once('./exif-function.php');

	// Exifを取得し、[$exif]に代入する
	$exif = @exif_read_data($_FILES['upfile']['tmp_name']);
	#echo $exif;

	$lat = 0;
	$lng = 0;

	//画像のExifの有無を判定
	if( $exif ){
		
		//GPS情報がない場合
		if(
			!isset( $exif['GPSLatitudeRef'] ) || empty( $exif['GPSLatitudeRef'] ) ||
			!isset( $exif['GPSLatitude'] ) || empty( $exif['GPSLatitude'] ) ||
			!isset( $exif['GPSLongitudeRef'] ) || empty( $exif['GPSLongitudeRef'] ) ||
			!isset( $exif['GPSLongitude'] ) || empty( $exif['GPSLongitude'] )
		){
			//撮影日時がある場合
			if( isset( $exif['DateTimeOriginal'] ) ){
				//撮影日時を取得
				$datetime = $exif['DateTimeOriginal'];	//$exif['DateTime']にすると画像を編集した日時に変更されてしまう
			}
		}

		else{
			// 緯度を60進数から10進数に変換する
			$lat = get_10_from_60_exif( $exif['GPSLatitudeRef'] , $exif['GPSLatitude'] );

			// 経度を60進数から10進数に変換する
			$lng = get_10_from_60_exif( $exif['GPSLongitudeRef'] , $exif['GPSLongitude'] );

			//撮影日時がある場合
			if( isset( $exif['DateTimeOriginal'] ) ){
				//撮影日時を取得
				$datetime = $exif['DateTimeOriginal'];	//$exif['DateTime']にすると画像を編集した日時に変更されてしまう
			}

			//echo '$exif[\'DateTimeOriginal\']: ' . $exif['DateTimeOriginal'] . '<br />';
			//echo 'datetime: ' . $datetime;

			// 案内メッセージ
			$alert = "<script type='text/javascript'> alert('画像に位置情報が含まれていました。'); </script>";
			echo $alert;
		}
	}//*/

echo "このファイル名：";
echo $image_title . "<br>";

echo "このファイルの拡張子：";
echo $ext . "<br>";

?>

<script type="text/javascript">
	//緯度経度の情報は数値データが必要、''で< ?php ?>はくくらない
	var lat=<?php echo $lat; ?>;
	var lng=<?php echo $lng; ?>;

	if(lat===0 && lng===0){
		var lat=null;
		var lng=null;
	}
	/*/データ送信確認用
    console.log('php側');
    console.log('lat:' + lat);
    console.log('lng:' + lng);
	*/
</script>

<!DOCTYPE html>
<html lang="ja">
        <head>
          <meta charset="UTF-8">
	        <title>データ投げ込みシステム(テスト)</title>
		  <meta name="viewport" content="width=device-width, initial-scale=1">
          <link rel="stylesheet" type="text/css" href="css/reset.css">
          <link rel="stylesheet" type="text/css" href="css/input.css">
		  <link rel="stylesheet" type="text/css" href="css/responsive_post2.css">
        </head>
        <body>

          <p class="head-form">入力フォーム</p>

	        <div class="input_area">
	           <form action="./confirm.php" method="post" enctype="multipart/form-data" id="contact_form">
	              <div class="item">
                          <label class="label">場所</label>
                          <div class="map_area">
                            <div id="map"></div>							
                            <div id="output"></div>
							<?php if($lat && $lng): ?>
								<input id="origin" type="button" value="元の位置"/>
							<?php endif; ?>
							<input type='hidden' id='Lat' name='Lat'>
							<input type='hidden' id='Lng' name='Lng'>
                          </div>
                  </div>
				  
				  <!--googleMapの表示に必要なJavaScriptの記述-->
				  <script src="js/jquery-3.3.1.min.js"></script>
                  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAOhP3cXmIts-lg7K2T2HUDCKpGpUq8nrI"></script>
                  <script src="js/index.js"></script>


	                <div class="item">
	                        <label class="label">内訳</label>
        	                <input id="rubble" type="checkbox" name="subject[]" value="rubble"><label for="rubble">がれき</label>
                	        <input id="wood" type="checkbox" name="subject[]" value="wood"><label for="wood">木くず</label>
                        	<input id="constWaste" type="checkbox" name="subject[]" value="constWaste"><label for="constWaste">建設廃棄物</label>
                        	<input id="plastWaste" type="checkbox" name="subject[]" value="plastWaste"><label for="plastWaste">廃プラスチック類</label>
                        	<input id="other" type="checkbox" name="subject[]" value="other"><label for="other">その他</label>
                	</div>

	                <div class="item">
        	                <label class="label">コメント</label>
	                        <textarea class="inputs" name="comment" placeholder="その他の内容など自由に書いてください。"><?php echo $comment;?></textarea>
	                </div>

	                <div class="item">
	                        <label class="label">写真</label>
							<div class="photo_area">
								<p><?php echo $image_title; ?></p>

								<!--参考元：PHPでの画像の保存・表示方法まとめ ブクマ位置:PHP-->
								<!--参考元：PHPでファイルを開いて読み込む ブクマ位置:PHP-->
                                <?php 
                                    if(preg_match("/image/",$mime)){
                                        $enc_img = base64_encode($image_data);
									    echo '<img class="photo-form" src="data:image/' . $ext . ';base64,'.$enc_img.'">';

                                    }elseif(preg_match("/video/",$mime)){
									    echo '<video controls class="photo-form" width="200" height="150" src=' . $image_path . '>';
                                    }
                                ?>
								<!--?php
									$enc_img = base64_encode($image_data);	//ファイルデータを64種類の英数字に変換、base64エンコードによってHTTPリクエストを減らせる
									echo '<img class="photo-form" src="data:image/' . $ext. ';base64,'.$enc_img.'">';	//srcに代入される内容が"data:/image/$ext;base64,$enc_img"ならOKって話
								?-->
							</div>
	                </div>

	                <div class="btn-area">
	                        <input type="reset" value="リセット"><input type="submit" value="入力内容確認">
			        </div>

					<!--Exif情報の中の高度と時刻を抽出-->
					<input type="hidden" name="altitude" value="<?php if(isset($altitude))echo $altitude; ?>">	<!--こっちはまだ手を付けない-->
					<input type="hidden" name="datetime" value="<?php if(isset($datetime))echo $datetime; ?>">

	           </form>
	        </div>
        </body>
</html>
