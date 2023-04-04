<?php

require_once("./properties.php");
require_once("./getFormAction.php");

$action = new getFormAction();

$page_flag = null;
// イベントID取得
if (isset($_POST['btn_confirm'])) {
	$page_flag = "save";
}
if (isset($_POST['btn_complete'])) {
	$page_flag = "post";
}

switch ($page_flag) {

	// データベースに保存後、完了ページに遷移
	case 'save':
		$action->saveDbPostData($_POST);
		require("./complete.php");

		break;

	//完了ページから入力ページに戻る際の分岐
	case 'post':
		require("./post.php");
		break;

	// 初回アクセス時、投稿画面表示
	default:
		require("./post.php");
		break;
} 
?>
