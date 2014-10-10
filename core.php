<?php
    
	class CORE{
		public static $config; // 設定參數
		public static $root; // 實體根目錄
		public static $db; // 資料庫
		
		function __construct(){
			self::$root = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;
			self::$config = include_once self::$root.'config/config.php';
			
			include_once self::$root.'TP/class.TemplatePower.inc.php';
			include_once self::$root.'view.php';
			include_once self::$root.'libs/db.php';
			
			include_once self::$root.'index.php';
			include_once self::$root.'member.php';
			
			self::permanent();
			self::auto_include();
		}
		
		// 自動 include
		private static function auto_include(){
			$include_filter = array(__CLASS__,'ROUTER'); // 針對根目錄檔案的過濾器，寫入不要 inlcude 的檔案
			$folder_filter = array('BAK','config',''); // 針對子目錄檔案的過濾器，寫入不要 inlcude 的目錄名稱
		}
		
		// 常駐程序
		public static function permanent(){
			self::$db = new DB(self::$config["connect"]);
			self::default_tag();
		}
		
		// 基本標記
		private static function default_tag(){
			VIEW::assignGlobal(array(
				"TAG_ROOT_PATH" => CORE::$config["root"],
				"TAG_FILE_PATH" => CORE::$config["file"],
				"TAG_IMAGE_PATH" => CORE::$config["img"],
				"TAG_CSS_PATH" => CORE::$config["css"],
				"TAG_JS_PATH" => CORE::$config["js"],
				"TAG_MANAGE_PATH" => CORE::$config["manage"],
			));
		}
		
		// 設定多語系是否使用同一個資料庫
		public static function db_sync($sync_status=false){
			if($sync_status){
				// 讀取主語系資料庫參數
				$lang_array = array_values(self::$config["lang"]);
				return $lang_array[0].'_';
			}else{
				return self::$config["prefix"];
			}
		}
	}
	
    
?>