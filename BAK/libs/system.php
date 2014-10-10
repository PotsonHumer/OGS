<?php
	session_start();
	
	$connect = array(
		'host' => 'localhost',
		'user' => 'hearthfarm.com',
		'pass' => 'zo4',
		'db' => 'hearthfarm.com'
	);
	
	include_once($_SERVER['DOCUMENT_ROOT']."/libs/class.TemplatePower.inc.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/libs/db.php");
	
	include_once($_SERVER['DOCUMENT_ROOT']."/config/card_cfg.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/config/main_cfg.php");
	
	// lang
	include_once($_SERVER['DOCUMENT_ROOT']."/lang/cht_lang.php");
	
	$db = new mysql($connect);
	$main_cfg = new MAIN_CFG;
	$card_cfg = new CARD_CFG;
?>