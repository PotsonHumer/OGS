<?php
    session_start();
    
	class CORE{
		public static $config; // 設定參數
		public static $root; // 實體根目錄
		public static $db; // 資料庫
		public static $path; // 目前 uri
		public static $lang; // 目前語系根目錄
		public static $manage; // 目前語系後台根目錄
		
		function __construct(){
			self::$root = self::real_path();
			self::$config = include_once self::$root.'config/config.php';
			
			self::auto_include();
			self::permanent();
		}
		
		// 常駐程序
		public static function permanent(){
			self::$db = new DB(self::$config["connect"]);
			//LANG::lang_fetch();
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
		
		// 基本標記
		protected static function default_tag(){
			VIEW::assignGlobal(array(
				"TAG_ROOT_PATH" => self::$lang,
				"TAG_MANAGE_PATH" => self::$manage,
				"TAG_ROOT_FILE" => CORE::$config["root"],
				"TAG_MANAGE_FILE" => CORE::$config["manage"],
				"TAG_FILE_PATH" => CORE::$config["file"],
				"TAG_IMAGE_PATH" => CORE::$config["img"],
				"TAG_CSS_PATH" => CORE::$config["css"],
				"TAG_JS_PATH" => CORE::$config["js"],
				"TAG_DISABLE" => 'disabled',
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
		
		// 載入外掛資源 (js,css), $custom_path => 自訂路徑
		public static function res_init(){
			global $cms_cfg,$tpl;
			
			static $box_title;
			static $css_title;
			static $js_title;
			static $custom_title;
			
			$new_title = func_get_args();
			$res_type = array_pop($new_title); // 最後一個值為資源類型
			
			switch($res_type){
				case "box":
					$res_tag = "TAG_JS_BOX";
					$res_title = 'box_title';
				break;
				case "css":
					$res_tag = "TAG_CSS_INCLUDE";
					$res_title = 'css_title';
				break;
				case "js":
					$res_tag = "TAG_JS_INCLUDE";
					$res_title = 'js_title';
				break;
				case "custom":
					$res_tag = "TAG_CUSTOM_INCLUDE";
					$res_title = 'custom_title';					
				break;
			}
			
			if(is_array($$res_title)){
				$$res_title = array_merge($$res_title,$new_title);
			}else{
				$$res_title = $new_title;
			}
			
			if(count($$res_title)){
				// 利用翻轉刪除重複的值
				$$res_title = array_flip($$res_title);
				$$res_title = array_flip($$res_title);
				
				foreach($$res_title as $key => $value){
					
					switch($res_type){
						case "box":
							$res_insert .= '<script src="'.CORE::$config["js"].'box_serial/'.$value.'_box.js" type="text/javascript"></script>'."\n";
						break;
						case "css":
							$res_insert .= '<link href="'.CORE::$config["css"].$value.'.css" rel="stylesheet" type="text/css" />'."\n";
						break;
						case "js":
							$res_insert .= '<script src="'.CORE::$config["js"].$value.'.js" type="text/javascript"></script>'."\n";
						break;
						case "custom":
							$value_array = explode(".",$value);
							$custom_type = array_pop($value_array);
							
							switch($custom_type){
								case "css":
									$res_insert .= '<link href="'.$value.'" rel="stylesheet" type="text/css" />'."\n";
								break;
								case "js":
									$res_insert .= '<script src="'.$value.'" type="text/javascript"></script>'."\n";
								break;
							}
						break;
					}
				}
				
				VIEW::assignGlobal($res_tag,$res_insert);
			}
		}

		// 顯示提示 (提示內容,提示後前往路徑,true => js 提示; false => tpl 提示)
		public static function notice($msg,$heading=false,$type=false){
			if(!$type){
				VIEW::assignGlobal("MSG_NOTICE",$msg);
				
				if(!empty($heading)){
					header('refresh: 2; url='.$heading);
				}
			}else{
				self::res_init('default','js');
				VIEW::assignGlobal("JS_NOTICE",'<script>js_notice("'.$msg.'","'.$heading.'");</script>');
			}
		}
		
		// 組成隨機密碼
		public static function rand_password(){
			$code_length = 12;
			$code_array = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			
			for($i=1;$i<=$code_length;$i++){
				$rand_code = rand(0, 26);
				
				if($rand_code <= 25){
					$code_word .= $code_array[$i];
				}else{
					$code_word .= rand(0, 9);
				}
			}
			
			return $code_word;
		}
		
		// 信件方法 (來源位置,寄送位置,內容,抬頭,寄件者名稱)
		public static function mail_handle($from,$to,$mail_content,$mail_subject,$mail_name){
	        $from_email=explode(",",$from);
	        $mail_subject = "=?UTF-8?B?".base64_encode($mail_subject)."?=";
	        //寄給送信者
	        $MAIL_HEADER   = "MIME-Version: 1.0\n";
	        $MAIL_HEADER  .= "Content-Type: text/html; charset=\"utf-8\"\n";
	        $MAIL_HEADER  .= "From: =?UTF-8?B?".base64_encode($mail_name)."?= <".$from_email[0].">"."\n";
	        $MAIL_HEADER  .= "Reply-To: ".$from_email[0]."\n";
	        $MAIL_HEADER  .= "Return-Path: ".$from_email[0]."\n";    // these two to set reply address
	        $MAIL_HEADER  .= "X-Priority: 1\n";
	        $MAIL_HEADER  .= "Message-ID: <".time()."-".$from_email[0].">\n";
	        $MAIL_HEADER  .= "X-Mailer: PHP v".phpversion()."\n";          // These two to help avoid spam-filters
	        $to_email = explode(",",$to);
	        for($i=0;$i<count($to_email);$i++){
	            if($i!=0 && $i%2==0){
	                sleep(2);
	            }
	            if($i!=0 && $i%5==0){
	                sleep(10);
	            }
	            if($i!=0 && $i%60==0){
	                sleep(300);
	            }
	            if($i!=0 && $i%600==0){
	                sleep(2000);
	            }
	            if($i!=0 && $i%1000==0){
	                sleep(10000);
	            }
	            @mail($to_email[$i], $mail_subject, $mail_content,$MAIL_HEADER);
			}
		}

	}
	
    
?>