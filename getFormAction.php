<?php

require_once("./properties.php");

class getFormAction {
	public $pdo;

	/**
	 * コネクション確保
	 */
	function __construct() {
		try {
			$this->pdo = new PDO( PDO_DSN, DATABASE_USER, DATABASE_PASSWORD,
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					]
				);
		} catch (PDOException $e) {
			echo 'error' . $e->getMessage();
			die();
		}
	}

	/**
	 * 入力データをDBに保存
	*/
	function saveDbPostData($data){

		// データの保存
		$stmt = $this->pdo->prepare('INSERT INTO db_test (lat,lng,subject,comment,datetime,image_title,image_path) values (:lat,:lng,:subject,:comment,:datetime,:image_title,:image_path)');

		$stmt->bindParam(':lat', $data['Lat'], PDO::PARAM_STR);
		$stmt->bindParam(':lng', $data['Lng'], PDO::PARAM_STR);

		$stmt->bindParam(':subject', $data['sub_d'], PDO::PARAM_STR);

		$stmt->bindParam(':comment', $data['comment'], PDO::PARAM_STR);

		$stmt->bindParam(':datetime', $data['datetime'], PDO::PARAM_STR);

		$stmt->bindParam(':image_title', $data['image_title'], PDO::PARAM_STR);

		//echo $data['image_path'] . '<br />';
		$stmt->bindParam(':image_path', $data['image_path'], PDO::PARAM_STR);

		$stmt->execute();
	}

	//保存された緯度経度情報を取得する関数
	function query(){
		$data = null;

		//sqlの作成と実行
		$sql = "SELECT * FROM db_test";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		//データの取得
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//jsonオブジェクト化
		return json_encode($data);
	}

}
?>
