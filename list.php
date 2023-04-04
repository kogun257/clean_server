<?php

require_once("./properties.php");
require_once("./getFormAction.php");
$action = new getFormAction();

//$data = $action->query();

//*
try {
  $pdo = new PDO( PDO_DSN, DATABASE_USER, DATABASE_PASSWORD,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]
    );

    $data = null;

    //sqlの作成と実行
    $sql = "SELECT * FROM db_test";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    //データの取得
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  echo 'error' . $e->getMessage();
  die();
}//*/

//print_r($data);

$data_length = count($data);

$datetime = [];
$datetime_str = [];
for($n=0; $n<$data_length; $n++){
    //撮影時刻があった場合は「年時日 時分秒」で表示
    if( $data[$n]['datetime'] != '1970-01-01 09:00:00' ){
        $datetime[$n] = explode(' ' , $data[$n]['datetime']);
        $date = explode('-' , $datetime[$n][0]);
        $time = explode(':' , $datetime[$n][1]);

	    $date_str = $date[0] . "年" . $date[1] . "月" . $date[2] . "日";
	    $time_str = $time[0] . "時" . $time[1] . "分" . $time[2] . "秒";

        $datetime_str[$n] = $date_str . ' ' . $time_str;
	}

    //もし1970-01-01 09:00:00だった場合は「時刻なし」と表示
    else{
        $datetime_str[$n] = '時刻なし';
	}

}

?>

<!DOCTYPE>
<html lang="ja">
      <head>
        <meta charset="UTF-8">
	    <title>データ投げ込みシステム(データ表示)</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="css/reset.css">
        <link rel="stylesheet" type="text/css" href="css/list.css">
        <link rel="stylesheet" type="text/css" href="css/responsive_list.css">
      </head>
        <body>
	        <div class="list_area">
                <div class="item">
                  <label class="label">緯度経度情報</label>
                  <div class="lmap_area">
  				 		      <div id="map"></div>
  				 	      </div>
                </div>
  					    <!--googleMapの表示に必要なJSファイル-->
  					    <script src="js/jquery-3.3.1.min.js"></script>
  					    <script src="https://maps.googleapis.com/maps/api/js?key= "></script>
  					    <script src="js/list.js"></script>

                <div class="item">
                  <label class="label">データ一覧</label>
                  <table border="1">
                    <tr>
                      <th>ID</th>
                      <!--th>緯度</th>
                      <th>経度</th-->
                      <th>内訳</th>
                      <th>コメント</th>
                      <th>撮影日時</th>
                      <th>画像ファイル名</th>
                    </tr>
                    <?php for ($i=0; $i<$data_length; $i++) : ?>
                      <tr>
                      <td class="id"><?php echo $data[$i]['id']; ?></td>
                      <!--td class="lat"><?php //echo $data[$i]['lat']; ?></td>
                      <td class="lng"><?php //echo $data[$i]['lng']; ?></td-->
                      <td class="subject"><?php
                        $subject = [];
                        $subject = explode(",",$data[$i]['subject']);

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

                       ?></td> <!--データベース用にimplodeしたsubjectの文字列はexplodeすれば配列に入れなおせる-->
                      <td class="comment"><?php echo $data[$i]['comment']; ?></td>
                      <td class="datetime"><?php echo $datetime_str[$i]; ?></td>
                      <td class="image"><a class="image" href="<?php echo $data[$i]['image_path']; ?>"><?php echo $data[$i]['image_title']; ?></a></td>
                      </tr>
                    <?php endfor; ?>
                  </table>
                </div>

            </div>

            <!--p class="testp">文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、文字列のテスト、</p-->
        </body>
</html>
