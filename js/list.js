$(function(){

//マップを表示するための関数
  function initMap(){
    // Google Mapで利用する初期設定用の変数
    var latlng = {lat: 40.60836724818774,  lng: 140.46372108227263};
    var opts = {
      zoom: 13,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      center: latlng
    };

    // getElementById("map")の"map"は、body内の<div id="map">より
    map = new google.maps.Map(document.getElementById("map"), opts);

    google.maps.event.addListener(map, 'click', event => clickListener());
  }

  //緯度経度を保存するための配列
  var markerD = [];

  $(function(){

      $.ajax({
        type:"POST",
        url:"data.php",
        dataType:"json",
        success:function(data){
          markerD = data;
          setMarker(markerD);
        },error: function(XMLHttpRequest, textStatus, errorThrown){
          alert('Error : ' + errorThrown);
        }
      });
  });

  var map;
  var infoWindow = [];
  var numberWindow = [];
  var marker = [];

  function setMarker(markerData) {
        for (var i = 0; i < markerData.length; i++) {

          var latNum = parseFloat(markerData[i]['lat']);
          var lngNum = parseFloat(markerData[i]['lng']);

          // マーカー位置セット
          var markerLatLng = new google.maps.LatLng({
          lat: latNum,
          lng: lngNum
          });

          //マップ上にマーカーを生成
          marker[i] = new google.maps.Marker({
            position: markerLatLng,
            map: map
          });

          // 表示文字列の生成(緯度・経度)
          var contentString = "ID:" + markerData[i]['id']
                            + "　緯度:" + markerData[i]['lat']
                            + " 経度:" + markerData[i]['lng'];

          var contentImage = "<img src=" + markerData[i]['image_path'] + " height='180'><br>";

          var contentAll = contentImage + contentString;

          // 情報ウィンドウの生成
          infoWindow[i] = new google.maps.InfoWindow({
              content: "<div id='infoWindow'>" + contentAll + "</div>"
          });

          numberWindow[i] = new google.maps.InfoWindow({
              content: "<div id='numberWindow'>ID:" + markerData[i]['id'] + "</div>"

          });

          markerinfoEvent(i);
      }
  }

  var openWindow;
  var tempWindow;
  // 情報ウインドウのクリックイベント時の表示関数
  function markerinfoEvent(i) {
    marker[i].addListener('click', function() {
      if(openWindow){
        openWindow.close();
      }
      // 情報ウィンドウの表示
      infoWindow[i].open(map, marker[i]);
      openWindow = infoWindow[i];
    });

    marker[i].addListener('mouseover', function(){
      numberWindow[i].open(map, marker[i]);
      tempWindow = numberWindow[i];
    });
    marker[i].addListener('mouseout', function(){
        tempWindow.close();
    });
  }

  function clickListener() {
    if(openWindow){
      openWindow.close();
    }
  }

  initMap();
});
