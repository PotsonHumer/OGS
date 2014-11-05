<?php

	$config["root"] = "/OGS/";
	$config["url"] = "localhost";
	
	// 初始路徑
	$config["img"] = $config["root"].'img/'; // 圖片路徑
	$config["css"] = $config["root"].'css/'; // css 路徑
	$config["js"] = $config["root"].'js/'; // js 路徑
	$config["file"] = $config["root"].'file/'; // 檔案路徑
	$config["manage"] = $config["root"].'ogsadmin/'; // 後台路徑
	$config["temp"] = CORE::$root.'temp'; // 樣板路徑
	
	// 雜項設定
	$config["sort"]	= 'asc'; // 資料庫排序
	$config["item_num"]	= 12; // 每分頁幾個項目
	$config["list_num"]	= 10; // 最多顯示幾個分頁連結
	
	// key : 語系名稱參數 , value : 語系資料庫參數
	$config["lang"] = array(
		'eng' => 'eng', // default
		'cht' => 'cht',
	);
	
	#### DON'T CHANGE THIS ####
	$lang_keys = array_keys($config["lang"]);
	
	$config["langfix"] = $lang_keys[0]; // 紀錄語系狀態變數
	$config["prefix"] = $config["lang"][$lang_keys[0]]; // 紀錄語系資料庫狀態變數
	###########################
	
	
	#### DB connect ####
	$config["connect"] = array(
		'host' => 'localhost',
		'user' => 'root',
		'pass' => 'studiodd',
		'db' => 'ogs'
	);
	####################
	
	
	#### autoload filter ####
	$config["file_filter"] = array('core','router');
	$config["dir_filter"] = array();
	##############################
	
	
	#### MEMBER ####
	$config["m_sync"] = false; // 多語系使用同個會員資料庫
	################
	
	$config["sess"] = 'ogs';
	
	return $config;
	
?>