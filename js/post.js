$(function() {
    const photo_exts = new Array('jpg', 'jpeg', 'png');
    const movie_exts = new Array('mp4', 'm4v', 'mov');

    //file-sampleクラスが変更されたときに実行
    $('#file-sample').on('change', function(e) {
        var fileName = e.target.files[0].name;
        
        //拡張子を比較するために小文字にする
        var ext = getExt(fileName).toLowerCase();

        console.log("fileName.ext:" + ext);

        console.log("何のファイル？");

        //画像と判定した場合
        if(photo_exts.indexOf(ext) != -1){
            console.log("画像やん");

            // 1枚だけ表示する
            var photo = e.target.files[0];

            // ファイルのブラウザ上でのURLを取得する
            var blobUrl = window.URL.createObjectURL(photo);

            // HTMLに書き出し (src属性にblob URLを指定)
            $('#file-preview').html('<img src="' + blobUrl + '">');
        }
        
        //動画と判断した場合
        else if(movie_exts.indexOf(ext) != -1){
            console.log("動画やん");
            
            var movie = e.target.files[0];
            
            // ファイルのブラウザ上でのURLを取得する
            var blobUrl = window.URL.createObjectURL(movie);

		    // HTMLに書き出し (src属性にblob URLを指定)
		    $('#file-preview').html('<video src="' + blobUrl + '" controls></video>');  
		}
        
        else{
            console.log("よう分からんわ");
        }
        //*/

    });

    function getExt(filename){
	    var pos = filename.lastIndexOf('.');
	    if (pos === -1) return '';
	    return filename.slice(pos + 1);
    }
});