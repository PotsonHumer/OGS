<?php

	class CHECK{
		private static $status;
		public static $result;
		public static $alert;
		
		function __construct(){} // no need
		
		public static function is_pass(){
			if(is_array(self::$status) && count(self::$status) == array_sum(self::$status)){
				self::$result = true;
			}else{
				self::$result = false;
			}
			
			self::check_clear();
			return self::$result;
		}
		
		public static function check_clear(){
			self::$status = '';
		}
		
		public static function is_email($args){
			$rs = preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $args);
			
			if(!$rs){
				self::$alert = '錯誤的 E-mail';
			}
			
			return self::$status[] = $rs;
		}
		
		public static function is_password($args){
			$rs = preg_match('/^[A-Za-z0-9]{4,20}/',$args);
			
			if(!$rs){
				self::$alert = '錯誤的密碼，請輸入 4 ~ 20 個字元的數字與英文';
			}
			
			return self::$status[] = $rs;
		}
		
		public static function is_same($args_1,$args_2){
			$rs = ($args_1 == $args_2)?true:false;
			
			if(!$rs){
				self::$alert = '數值錯誤';
			}
			
			return self::$status[] = $rs;
		}
		
		public static function is_array_exist($args){
			$rs = (is_array($args) && count($args) > 0)?true:false;
			
			if(!$rs){
				self::$alert = '陣列不存在';
			}
			
			return self::$status[] = $rs;
		}
		
		public static function is_number(){
			$args = func_get_args();
			
			if(self::is_array_exist($args)){
				foreach($args as $key => $value){
					$rs[$key] = preg_match('/\d+/', $value);
				}
				
				if(self::is_array_exist($rs) && count($rs) == array_sum($rs)){
					$total_rs = true;
				}else{
					$total_rs = false;
				}
				
				return self::$status[] = $total_rs;
			}
		}
	}
	

?>