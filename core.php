<?php
    session_start();
    
	class CORE{
		public static $config; // 設定參數
		public static $root; // 實體根目錄
		public static $db; // 資料庫
		
		function __construct(){
			self::$root = self::real_path();
			self::$config = include_once self::$root.'config/config.php';
			
			self::auto_include();
			self::permanent();
		}
		
		// 定義當前目錄位置
		public function real_path($__file=__FILE__,$addon=''){
			return realpath(dirname($__file)).DIRECTORY_SEPARATOR.$addon;
		}
				
		// 自動 include
		private static function auto_include(){
			$file_filter = self::$config["file_filter"]; // 針對根目錄檔案的過濾器，寫入不要 inlcude 的檔案
			$folder_filter = self::$config["dir_filter"]; // 針對子目錄檔案的過濾器，寫入不要 inlcude 的目錄名稱
			
			// include 檔案
			$files = glob(self::$root.'*.php');
			foreach($files as $f_key => $f_path){
				$f_name = str_replace(self::$root, '', $f_path);
				$f_name = str_replace('.php', '', $f_name);
				
				if(!in_array($f_name,$file_filter)){
					include_once $f_path;
				}
			}
			
			// include 目錄內檔案
			// 目錄內如有 summon.php, auto_include 會在此 include
			$dirs = glob(self::$root.'*', GLOB_ONLYDIR);
			foreach($dirs as $d_key => $d_path){
				$d_name = str_replace(self::$root, '', $d_path);
				$summon = file_exists($d_path.DIRECTORY_SEPARATOR.'summon.php');
				
				if(!in_array($d_name,$folder_filter) && $summon){
					include_once $d_path.DIRECTORY_SEPARATOR.'summon.php';
				}
			}
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
		
		// 清除所有 SESSION, $custom_path => 指定前往路徑
		public static function full_logout($custom_path=''){
			$path = (!empty($custom_path))?$custom_path:CORE::$config["root"];
			//unset($_SESSION[CORE::$config["sess"]]);
			session_destroy();
			header("location: ".$path);
		}
	}
	
    
?>