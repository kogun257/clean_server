<?php

$uploaddir = '/var/www/html/project_movie/tmp/t_img/';
$upload = $uploaddir . basename($_FILES['upfile']['name']);
if(!move_uploaded_file($_FILES['upfile']['tmp_name'], $upload)){
  echo "ファイル保存時にエラーが発生しました。<br>";
}

echo "isset:";
echo isset($_FILES['upfile']['error']) ? "データが入っています。<br />" : "データなし<br />";

echo "is_int:";
echo is_int($_FILES['upfile']['error']) ? "整数型です。<br />" : "データなし<br />";

echo "<br>(\$_FILES):";
var_dump($_FILES);
echo "<br><br>";

if ($_FILE['upfile']['error'] != UPLOAD_ERR_OK) {
    echo "エラーの中身を表示<br />";

    switch ($_FILE['upfile']['error']) {
      case UPLOAD_ERR_INI_SIZE:
        return "アップロードされたファイルは、upload_max_filesizeディレクティブの値(".ini_get(‘upload_max_filesize’).")を超えています";
      case UPLOAD_ERR_FORM_SIZE:
        return "アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています";
      case UPLOAD_ERR_PARTIAL:
        return "アップロードされたファイルは一部のみしかアップロードされていません";
      case UPLOAD_ERR_NO_FILE:
        return "ファイルはアップロードされませんでした";
      case UPLOAD_ERR_NO_TMP_DIR:
        return "テンポラリフォルダがありません";
      case UPLOAD_ERR_CANT_WRITE:
        return "ディスクへの書き込みに失敗しました";
      case UPLOAD_ERR_EXTENSION:
        return "PHPの拡張モジュールがファイルのアップロードを中止しました";
      default:
        return "未知エラー";
      }
}else{echo "UPLOAD_ERR_OK<br />";}

require("./upfile_test.php");

?>