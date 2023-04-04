$(function(){
  //緯度経度を保存するための配列
  var markers = [];

  //初期値となるexif情報の緯度経度
  const exif_latlng = {lat, lng};

//*マップを表示するための関数
  function initMap(){
    // Google Mapで利用する初期設定用の変数
    if (lat!=null && lng!=null) {
        var latlng = {lat,lng};
        
        //form要素にPOST送信用のinput要素を追加
        postForm(lat,lng);
    }else{
        var latlng = {lat: 40.60761448292698,  lng: 140.46424477093825};    //弘前城の緯度経度
    }

    /*/データ受け取り確認用
    console.log('js側');
    console.log(latlng);
    console.log('lat:' + lat);
    console.log('lng:' + lng);
            
    console.log('latlng.lat:' + latlng.lat);
    */
     
    var opts = {
      zoom: 13,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: latlng
    };

    // getElementById("map")の"map"は、body内の<div id="map">より
    var map = new google.maps.Map(document.getElementById("map"), opts);

    //マーカー表示用
    if(lat!=null){
        var marker = new google.maps.Marker({
          position: latlng,
          map: map
        });

        // 生成されたマーカーを保存
        markers.push(marker);
    }

    google.maps.event.addListener(map, 'click', event => clickListener(event, map));

    //「元の位置」ボタン
    $('#origin').on('click', map, function(){
        deleteMarkers();

        origin_button(map);
        document.getElementById("output").innerHTML = '元の位置に戻りました';
    });

  }

  //「元の位置」ボタン
  function origin_button(map){
    lat = exif_latlng.lat;
    lng = exif_latlng.lng;

    /*/データ受け取り確認用
    console.log('exif_lat:' + lat);
    console.log('exif_lng:' + lng);

    console.log(exif_latlng);
    */

    //*
    var marker = new google.maps.Marker({
      position: exif_latlng,
      map: map
    });

    // 生成されたマーカーを保存
    markers.push(marker);
    
    //Exif情報の位置が中心になるようにマップを移動
    map.panTo(new google.maps.LatLng(lat, lng));

    //form要素にPOST送信用のinput要素を追加
    postForm(lat, lng);

  }

//クリックしたときにマーカーを表示する関数
  function clickListener(event, map) {
    //元々あるマーカーをマップ上から削除
    deleteMarkers();

    //マップ上のクリックした位置でマーカーを生成
    var marker = new google.maps.Marker({
      position: event.latLng,
      map: map
    });

    //event.latLngから緯度と経度をそれぞれ保存
    lat = event.latLng.lat();
    lng = event.latLng.lng();

    //console.log(lat);
    //console.log(lng);

    //form要素にPOST送信用のinput要素を追加
    postForm(lat,lng);

    //クリックした位置が中心になるようにマップを移動
    map.panTo(new google.maps.LatLng(lat,lng));

    // 情報ウインドウの生成とクリックイベント関数の登録処理
    setMarkerListener(marker, event.latLng);

    // 生成されたマーカーを保存
    markers.push(marker);

    document.getElementById("output").innerHTML = '新しい地点が選択されました';
  }


  // 情報ウインドウの生成とクリックイベント関数の登録処理
  function setMarkerListener(marker, location) {
      // 表示文字列の生成(緯度・経度)
      var contentString = "lat:" + location.lat() + "<br>"
                        + "lng:" + location.lng();
      // 情報ウィンドウの生成
      var infoWindow = new google.maps.InfoWindow({
          content: contentString,
          maxWidth: 200
      });
      // マーカーのクリックイベントの関数登録
      google.maps.event.addListener(marker, 'click', function(event) {
          // 情報ウィンドウの表示
          infoWindow.open(map, marker);
      });
  }


  // マップからマーカーの画像を消し、マーカーも削除
  function deleteMarkers() {
    if (markers) {
      for (i in markers)markers[i].setMap(null);  //マップ上にクリックした位置のマーカーは情報を一度配列に入れないとをsetMap(null)で消せない
      markers.length = 0;
    }
  }

//参考元：入力フォームに値をセットする方法　ブクマ位置:Javascript
//javascriptから動的にidがLatとLngのinput要素にデータを配置する
  function postForm(Lat,Lng) {
    document.getElementById('Lat').value = Lat;
    document.getElementById('Lng').value = Lng;
  }

  initMap();
});
