<?php

	class MEMBER{
		private static $db_prefix;
		private static $func;
		private static $is_login;

		function __construct($args){
			
			self::$db_prefix = CORE::db_sync(CORE::$config["m_sync"]); // 判斷是否使用同個資料庫
			$is_login = (!empty($_SESSION[CORE::$config["sess"]]["member"]["m_id"]))?true:false; // 檢查是否登入
			
			// 處理參數
			self::$func = array_shift($args);
			if(count($args) > 0){
				self::args_handle($args);
			}

			switch(self::$func){
				case "login":
					self::login();
				break;
				default:
					if($is_login){
						
					}else{
						new VIEW("ogs-login-tpl.html");
					}
				break;
			}
		}
		
		// 參數處裡
		private static function args_handle($args){
			
		}
		
		// 登入
		private static function login(){
			$check = preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $_POST["m_account"]); // m_account
			$check  = preg_match('/^[A-Za-z0-9]{4,20}/',$_POST["m_password"]); // m_password
			
			if(!$check){
				// 登入失敗
				CORE::full_logout();
			}
			
			$m_password_md5 = md5($_POST["m_password"]);
			
			$select = array (
				'table' => self::$db_prefix.'_member',
				'fields' => "*",
				'condition' => "m_account = '".$_POST["m_account"]."' and m_password = '".$m_password_md5."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				// 登入成功
				$row = DB::field($sql);
				unset($row["m_password"]);
				
				foreach($row as $m_field => $m_value){
					$_SESSION[CORE::$config["sess"]]["member"][$m_field] = $m_value;
				}
				
				header("location: ".CORE::$config["root"].'member/');
			}else{
				// 登入失敗
				CORE::full_logout();
			}
		}
	}
	
?>