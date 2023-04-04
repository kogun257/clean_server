<!--postからデータを変数に移行-->
<?php
  //参考元：3分で覚え直す$_SESSIONの使い方まとめ [PHP]　ブクマ位置:php
  //参考元：PHPで 「Webページの有効期限が切れてます」となる時の傾向と対策　ブクマ位置:php
  //データ受け渡し用にsession開始
    session_start([
        'cookie_lifetime' => 600,   //セッションの有効期限を10分に設定
    ]);
    header('Expires: -1');
    header('Cache-Control:');
    header('Pragma:');

require_once("./properties.php");

  //緯度経度
  $lat = null;
  $lng = null;
  //被写体
  $subject = null;
  //コメント
  $comment = 'なし';
  //撮影日時
  $datetime = '';
  //echo $datetime;
  //画像ファイル名
  $image_title = null;
  $image_data = null;
  $image_ext = null;

  if($_POST['Lat']){
    $lat = $_POST['Lat'];
    $lat = round($lat, 6);  //小数点6桁までを代入
  }
  if($_POST['Lng']){
    $lng = $_POST['Lng'];
    $lng = round($lng, 6);  //小数点6桁までを代入
  }

  if(isset($_POST['subject'])){
    $subject = $_POST['subject'];
  }
  if(isset($_POST['comment']) && ($_POST['comment'] != '')){
    $comment = $_POST['comment'];
  }
  if(isset($_POST['datetime'])){
    $datetime = date('Y-m-d H:i:s' , strtotime( $_POST['datetime'] ));
    //echo $datetime;
  }else{
      $datetime = date('Y-m-d H:i:s' , strtotime(0));  //mysqlではdatetime型のデータしか受け付けてない(Y-m-d H:i:sという型)
  }


  if(isset($_SESSION['image_title'])){
    $image_title = $_SESSION['image_title'];
	  $image_data = $_SESSION['image_data'];
    $image_tmp = $_SESSION['image_tmp'];
    $image_ext = $_SESSION['image_ext'];

    //echo 'セッションにデータ入ってたわ' . '<br />';
  }else{echo 'セッションにデータ無いで';}

  
  $enc_img = base64_encode($image_data);
  $imginfo = getimagesize('data:application/octet-stream;base64,' . $enc_img);
  
  //*
  try {
        //ファイル名を年月日に変更して、ファイル名の重複を避けかつ検索性を向上させる
        $up_date = '_' . date("Ymd");

        // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し，保存する        
        if (!file_put_contents(
                $image_path = sprintf('tmp/s_img/%s%s.%s',
                sha1($image_tmp),
                $up_date,
                $image_ext
                ),
                $_SESSION['image_data'],
                LOCK_EX
                )
        ) {
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }

        // ファイルのパーミッションを確実に0644に設定する
        chmod($image_path, 0644);
        //echo 'image_path:' . $image_path . '<br />';

  } catch (RuntimeException $e) {

    exit($e->getMessage());
  }//*/

  //echo '<br />';
  //echo '拡張子:' . $image_ext;

  /*スクリプト対策*/
  $comment = htmlspecialchars($comment,ENT_QUOTES,'UTF-8');

   /*未入力チェック*/
  $errmsg = '';
  $errno = 0; //未入力項目の数を保存
  $errnom = '送信内容が入力されていません。';//何も入力されていないときに表示--①
  if($lat === null){
    $errmsg = $errmsg.'<p>場所が選択されていません。</p>';
    $errno++;
  }
  if($subject === null){
    $errmsg = $errmsg.'<p>被写体が入力されていません。</p>';
    $errno++;
  }

?>

<!DOCTYPE>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
      <title>データ投げ込みシステム(テスト)</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" href="css/reset.css">
      <link rel="stylesheet" type="text/css" href="css/input.css">
      <link rel="stylesheet" type="text/css" href="css/responsive_confirm.css">
  </head>
  <body>

  <p class="head-form">確認ページ</p>

  <form action="./index2.php" method="post" id="confirm_form">

  <!--
  入力データを確認のために表示
  -->

  <?php if(($errmsg != '') && ($errno < 2)): ?> <!--場所、被写体のどちらかの項目が未入力の時にerrmsgを表示-->
    <div class="input_area">
        <p><?php echo $errmsg;?></p>
        <div class="btn-area">
          <button type="button" onclick=history.back()>戻る</button>
        </div>
    </div>

  <?php elseif($errno === 2): ?>  <!--場所と内訳の項目が未入力の時にerrnomを表示-->
    <div class="input_area">
        <p><?php echo $errnom;?></p>
        <div class="btn-area">
          <button type="button" onclick=history.back()>戻る</button>
        </div>
    </div>
  <?php else: ?>

  <!--場所の表示-->
  <div class="input_area">
    <div class="item">
      <label class="label">場所</label>
      <div class="map_area">
        <div id="map"></div>
      </div>      
      <!--googleMapの表示に必要なJavaScriptの記述-->
      <script>

        var lat = <?php echo $lat;?>;
        var lng = <?php echo $lng;?>;

        //データ受け取り確認用
        console.log('php側');
        console.log('lat:' + lat);
        console.log('lng:' + lng);

        var image_title = '<?php echo $image_title;?>';
        console.log('image_title:' + image_title)

         function initMap(){
           // Google Mapで利用する初期設定用の変数
           var latlng = {lat,lng}
           var opts = {
             zoom: 13,
             mapTypeId: google.maps.MapTypeId.ROADMAP,
             center: latlng
           };

           // getElementById("map")の"map"は、body内の<div id="map">より
           var map = new google.maps.Map(document.getElementById("map"), opts);

           var marker = new google.maps.Marker({
             position: {lat,lng},
             map: map
           });

        }
      </script>
      <script src="//maps.googleapis.com/maps/api/js?key= =initMap" async></script>
    </div>

  <!--被写体の表示-->
    <div class="item">
      <label class="label">内訳</label>
      <p class="textp"><?php
      $n = 0;

      foreach($subject as $value){
        if ($value != null && $n > 0) echo ",";
        if ($value == 'rubble') echo "がれき";
        if ($value == 'wood') echo "木くず";
        if ($value == 'constWaste') echo "建設廃棄物";
        if ($value == 'plastWaste') echo "廃プラスチック類";
        if ($value == 'other') echo "その他";
        $n++;
      }
      ?></p>
    </div>

    <!--コメントの表示-->
      <div class="item">
        <label class="label">コメント</label>
        <p class="textp"><?php echo $comment; ?></p>
      </div>


    <div class="item">
      <label class="label">画像</label>
      <div class="photo_area">
        <p class="textp"><?php echo $image_title; ?></p>
        <!--参考元：PHPでの画像の保存・表示方法まとめ ブクマ位置:PHP-->
		<!--参考元：PHPでファイルを開いて読み込む ブクマ位置:PHP-->
        <?php
        
          //echo $image_ext;
          switch ($image_ext){
            case 'jpg':
            case 'png':
            case 'gif':
              $enc_img = base64_encode($image_data);
              echo '<img class="photo-form" src="data:image/' . $image_ext . ';base64,'.$enc_img.'">';
              break;

            case 'mp4':
            case 'mov':
              echo '<video controls class="photo-form" width="200" height="150" src=tmp/t_img/' . basename($image_path) . '>';
              break;
          }
            
          // echo '<img class="photo-form" src="data:image/' . $image_ext . ';base64,'.$enc_img.'">';
        ?>
      </div>
    </div>


    <div class="btn-area">
      <button type="button" onclick=history.back()>戻る</button>
      <input type="submit" name="btn_confirm" value="上記の内容で送信する">
    </div>
  </div>
    <!--
    入力データをPOST送信
    -->

    <!--googleMapの緯度経度-->
    <input type="hidden" name="Lat" value="<?php echo $lat; ?>">
    <input type="hidden" name="Lng" value="<?php echo $lng; ?>">

      <!--配列の送信①
      <?php //foreach($subject as $sub_d):?>
      <input type="hidden" name="sub_d[]" value="<?php //echo $sub_d; ?>">
      <?php //endforeach;?>
      -->

    <!--配列の送信②-->
    <!--被写体-->
    <?php $sub_d = implode(",",$_POST['subject']); ?>
    <input type="hidden" name="sub_d" value="<?php echo $sub_d; ?>">

      <!--配列をPOST送信するにはforeachで配列ごと送信①、もしくはimplodeで一つにまとめて送信②-->

    <!--コメント-->
    <input type="hidden" name="comment" value="<?php echo $comment; ?>">

    <!--撮影日時-->
    <input type="hidden" name="datetime" value="<?php echo $datetime; ?>">

    <!--ファイル名とファイルパス-->
    <input type="hidden" name="image_title" value="<?php echo $image_title; ?>">
    <input type="hidden" name="image_path" value="<?php echo $image_path; ?>">

    
  <?php endif; ?>

  </form>
  </body>
</html>
