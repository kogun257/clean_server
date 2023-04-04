<?php
//mysqlへの接続用
define('DATABASE_NAME','test');
define('DATABASE_USER','server');
define('DATABASE_PASSWORD','hikaru12');
define('DATABASE_HOST','localhost');

define('PDO_DSN','mysql:dbname=' . DATABASE_NAME .';host=' . DATABASE_HOST . '; charset=utf8mb4');

//ディレクトリ名の指定用
define('TMP_DIR','./tmp/t_img/');
define('SAVE_DIR','./tmp/s_img/');
define('USB_DIR','/media/pi/USB/img/');

?>
