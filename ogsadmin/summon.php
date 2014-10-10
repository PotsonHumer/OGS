<?php

	class OGSADMIN{
		protected static $root;  // 後台實體根目錄
		protected static $temp;  // 後台樣板目錄
		protected static $is_login; // 是否登入, true => 登入, false => 未登入
		protected static $func; // 功能名稱
		
		function __construct($args){
			
			self::$root = CORE::real_path(__FILE__); // 後台根目錄
			self::$temp = self::$root.'temp'.DIRECTORY_SEPARATOR; // 後台樣板目錄
			self::$is_login = (!empty($_SESSION[CORE::$config["sess"]]["ogsadmin"]["oa_id"]))?true:false; // 檢查是否登入
			self::$func = array_shift($args); // 功能名稱
			
			switch(self::$func){
				// 登入
				case "login":
					if(!self::$is_login){
						self::login();
					}else{
						header("location: ".CORE::$config["manage"]);
					}
				break;
				
				// 登出
				case "logout":
					CORE::full_logout(CORE::$config["manage"]);
				break;
				
				default:
					self::login_check();
					//new VIEW
				break;
			}
		}
		
		// 檢查登入
		protected static function login_check(){
			// 未登入、顯示登入頁
			if(!self::$is_login){
				$temp_option = array("MAIN" => self::$temp.'ogs-admin-login-tpl.html');
				new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
				exit;
			}else{
				
			}
		}
		
		// 後台登入
		private static function login(){
			$check = preg_match('/^[A-Za-z0-9]{4,20}/', $_POST["oa_account"]); // oa_account
			$check  = preg_match('/^[A-Za-z0-9]{4,20}/',$_POST["oa_password"]); // oa_password
			
			if(!$check){
				// 登入失敗
				CORE::full_logout(CORE::$config["manage"]);
			}
			
			$oa_password_md5 = md5($_POST["oa_password"]);
			
			$select = array (
				'table' => 'ogs_admin',
				'fields' => "*",
				'condition' => "oa_account = '".$_POST["oa_account"]."' and oa_password = '".$oa_password_md5."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				// 登入成功
				$row = DB::field($sql);
				unset($row["oa_password"]);
				
				foreach($row as $oa_field => $oa_value){
					$_SESSION[CORE::$config["sess"]]["ogsadmin"][$oa_field] = $oa_value;
				}
				
				header("location: ".CORE::$config["manage"]);
			}else{
				// 登入失敗
				CORE::full_logout(CORE::$config["manage"]);
			}
		}
	}
?>