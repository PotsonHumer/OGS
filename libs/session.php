<?php

	// 方便使用 Session class
	class SESS{
		
		public static $val; // 輸出 get 處理的值
		private static $act; // 動作標記
		private static $args; // 紀錄輸入值
		private static $write; // 寫入值
		
		function __construct(){} // No need
		
		// 取得 session
		public static function get(){
			
			CHECK::is_array_exist(self::$args);
			
			if(CHECK::is_pass()){
				$args = self::$args;
			}else{
				$args = func_get_args();
			}
			
			CHECK::is_array_exist($args);
			
			if(CHECK::is_pass()){
				foreach($args as $key_title){
					$keys_array[] = '["'.$key_title.'"]';
				}
				
				$keys_str = implode("",$keys_array);
				
				switch(self::$act){
					case "write":
						foreach(self::$write as $write_key => $write_value){
							eval(
								'$_SESSION[CORE::$config["sess"]]'.$keys_str.'["'.$write_key.'"] = "'.$write_value.'";'
							);
						}
					break;
					case "del":
						eval(
							'unset($_SESSION[CORE::$config["sess"]]'.$keys_str.');'
						);
						return true;
					break;
					default:
						eval(
							'$output = $_SESSION[CORE::$config["sess"]]'.$keys_str.';'
						);
						
						self::$val = $output;
						return $output;
					break;
				}
			}else{
				return false;
			}
		}
		
		// 寫入 session
		public static function write(){
			
			$args = func_get_args();
			self::$write = array_pop($args); // 最後一個輸入為寫入值
			self::$act = 'write';
			self::$args = $args;

			CHECK::is_array_exist(self::$write);
			
			if(CHECK::is_pass()){
				self::get();
			}
			
			self::$act = false;
			self::$args = false;
			self::$write = false;
		}
		
		// 刪除 session
		public static function del(){
			self::$act = 'del';
			self::$args = func_get_args();
			
			self::get();
			
			self::$act = false;
			self::$args = false;
		}
	}

?>