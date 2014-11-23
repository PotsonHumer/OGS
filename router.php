<?php

	include_once './core.php';

	class ROUTER extends CORE{
		function __construct(){
			parent::__construct();
			
			// 去除根目錄
			if(self::$config["root"] == "/"){
				$uri = preg_replace("/^\//", '', $_SERVER["REQUEST_URI"],1);
			}else{
				$uri = str_replace(self::$config["root"], '', $_SERVER["REQUEST_URI"]);
			}
			
			CORE::$path = $uri; // 記錄當下 uri
			
			// 解析位置
			if(!empty($uri)){
				$uri_array = explode("/",$uri);
				$uri_array = self::lang_switch($uri_array);
				self::func_switch($uri_array);
			}else{
				// 首頁
				self::$lang = CORE::$config["root"];
				CORE::default_tag();
				self::temp_path(); // 重組樣板路徑
				new INDEX;
			}
			
			// 紀錄最後顯示的列表路徑
			$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$config["root"].CORE::$path;
		}
		
		// 語系偵測
		function lang_switch($uri_array){
			$lang_meter = $uri_array[0];
			
			if(is_array(self::$config["lang"])){
				
				// 解析語系設定
				foreach(self::$config["lang"] as $lang => $prefix){
					$i++;
					
					if($lang == $lang_meter){
						self::$config["langfix"] = $lang; // 語系設定
						self::$config["prefix"] = $prefix; // 語系資料庫設定
						$uri_shift = true;
					}
				}
				
				if($uri_shift && !empty(self::$config["langfix"])){
					self::$lang = CORE::$config["root"].self::$config["langfix"].'/';
					self::temp_path(); // 重組樣板路徑
					array_shift($uri_array);
				}else{
					self::$lang = CORE::$config["root"];
					self::temp_path(); // 重組樣板路徑
				}
				
				self::$manage = self::$lang.CORE::$config["manage"];
				CORE::default_tag();
			}
			
			return $uri_array;
		}
		
		// 功能偵測
		private static function func_switch($uri_array){
			
			// 刪除尾空值
			if(empty($uri_array[count($uri_array) - 1])){
				array_pop($uri_array);
			}
			
			// 解析功能參數
			if(count($uri_array) > 0 && is_array($uri_array)){
				
				// 取出第一順位參數
				$class_name = array_shift($uri_array);
				$class_name = strtoupper($class_name);
				
				// 檢查是否有頁次參數並處理
				if(count($uri_array) > 0){
					$uri_array = self::page_handle($uri_array);
				}
				
				// 實體化主要程序
				if(class_exists($class_name)){
					new $class_name($uri_array);
				}else{
					trigger_error("Class ".$class_name." doesn't exists");
				}
				
				# 參數基準
				# 0 => 主要程序 (aboutus,products,member...etc)
				# 1 => 功能參數 (new,regist,send....etc)
				# 2 ~ => 其他參數
				# ~ last => 頁次參數 (1,2,3,4....)
				
			}else{
				// 無參數前往首頁
				new INDEX;
			}
		}
		
		// 頁次處裡
		private static function page_handle(array $args){
			$origin_args = $args;
			$last_arg = array_pop($args);
			
			if(preg_match('/page-/', $last_arg)){
				PAGE::$page_args = $last_arg; // 載入頁次參數
				return $args;
			}else{
				return $origin_args;
			}			
		}
		
		// 重組樣板路徑
		public static function temp_path(){
			self::$config["temp"] = self::$config["temp"].'_'.self::$config["langfix"].DIRECTORY_SEPARATOR;
		}
		
		// 錯誤處理
		public static function error_handle($error_no,$error_str){
			// log to console

			new VIEW("404.htm",false,false,true,true);
			echo '<script> try{ console.log("('.$error_no.') '.$error_str.'"); }catch(e){ alert("'.$error_no.') '.$error_str.'"); } </script>';
			exit;
		}
	}
	
	set_error_handler('ROUTER::error_handle',E_USER_NOTICE);
	$router = new ROUTER;
	
?>