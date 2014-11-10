<?php

	class OGSADMIN{
		protected static $root;  // 後台實體根目錄
		protected static $temp;  // 後台樣板目錄
		protected static $is_login; // 是否登入, true => 登入, false => 未登入
		protected static $func; // 功能名稱
		
		function __construct($args){
			
			new LANG; // 語系功能
			
			self::$root = CORE::real_path(__FILE__); // 後台實體根目錄
			self::$temp = self::$root.'temp'.DIRECTORY_SEPARATOR; // 後台樣板目錄
			
			// 檢查是否登入
			self::$is_login = (!empty($_SESSION[CORE::$config["sess"]]["ogsadmin"]["oa_id"]))?true:false;
			CORE::res_init(CORE::$config["root"].CORE::$config["manage"].'css/ogsadmin.css','custom'); // 載入 CSS
			CORE::res_init('font','css'); // 載入 CSS
			CORE::res_init('default','js');
			self::$func = array_shift($args); // 功能名稱
			
			// include 所有模組
			$file_array = glob(self::$root.'*.php');
			if(is_array($file_array) && count($file_array) > 1){
				foreach($file_array as $file_key => $file_path){
					if(!preg_match('/(summon.php)/',$file_path)){
						include_once $file_path;
					}
				}
			}
			
			// 預先執行部分
			if(self::$is_login){
				VIEW::newBlock("TAG_HEADER_OPTION");
			}
			
			// 功能選擇
			switch(self::$func){
				// 登入
				case "login":
					if(!self::$is_login){
						self::login();
					}else{
						header("location: ".CORE::$manage);
					}
				break;
				
				// 取回帳號密碼
				case "forget":
					if(!self::$is_login){
						self::forget_password($args);
						$temp_option = array("MAIN" => self::$temp.'ogs-admin-forget-tpl.html');
						new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
					}else{
						header('location: '.CORE::$manage);
					}
				break;
				
				// 登出
				case "logout":
					CORE::full_logout(CORE::$manage);
				break;
				
				case '':
					CORE::res_init(CORE::$manage.'css/login.css','custom');
					self::login_check();
					self::index();
				break;
				
				default:
					self::login_check();
					$class_name = strtoupper(self::$func);
					if(class_exists($class_name)){
						new $class_name($args);
					}else{
						new VIEW(CORE::$root."404.htm",false,false,true);
					}
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
				$oa_id = $_SESSION[CORE::$config["sess"]]["ogsadmin"]["oa_id"];
				$oa_account = $_SESSION[CORE::$config["sess"]]["ogsadmin"]["oa_account"];
				
				$select = array (
					'table' => 'ogs_admin',
					'field' => "*",
					'where' => "oa_account = '".$oa_account."' and oa_id = '".$oa_id."' and oa_status = '1'",
					//'order' => '',
					//'limit' => '',
				);
				
				$sql = DB::select($select);
				$rsnum = DB::num($sql);
				
				if(empty($rsnum)){
					self::$is_login = false;
					self::login_check();
				}
			}
		}
		
		// 後台登入
		private static function login(){
			$check = preg_match('/^[A-Za-z0-9]{4,20}/', $_POST["oa_account"]); // oa_account
			$check  = preg_match('/^[A-Za-z0-9]{4,20}/',$_POST["oa_password"]); // oa_password
			
			if(!$check){
				// 登入失敗
				CORE::full_logout(CORE::$manage);
			}
			
			$oa_password_md5 = md5($_POST["oa_password"]);
			
			$select = array (
				'table' => 'ogs_admin',
				'field' => "*",
				'where' => "oa_account = '".$_POST["oa_account"]."' and oa_password = '".$oa_password_md5."' and oa_status = '1'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				// 登入成功
				$row = DB::fetch($sql);
				unset($row["oa_password"]);
				
				foreach($row as $oa_field => $oa_value){
					$_SESSION[CORE::$config["sess"]]["ogsadmin"][$oa_field] = $oa_value;
				}
				
				header("location: ".CORE::$manage);
			}else{
				// 登入失敗
				CORE::full_logout(CORE::$manage);
			}
		}
		
		// 取回帳號密碼
		private static function forget_password(array $args){
			$email_ck = preg_match('/^[A-Za-z0-9]{4,20}/', $_POST["oa_email"]);
						
			// 發送信件
			if($email_ck && $args[0] == 'send'){
				$select = array(
					'table' => 'ogs_admin',
					'field' => "*",
					'where' => "oa_email = '".$_POST["oa_email"]."'",
					//'order' => '',
					//'limit' => '',
				);
				
				$sql = DB::select($select);
				$rsnum = DB::num($sql);
				
				if(!empty($rsnum)){
					$row = DB::fetch($sql);
					$rand_password = CORE::rand_password(); // 組成隨機密碼
					
					// 更改帳號為隨機密碼
					$new_password = md5($rand_password);
					$sql_update = array(
						'oa_password' => $new_password,
						'oa_id' => $row["oa_id"],
					);
					
					DB::update('ogs_admin',$sql_update);

					// 發送信件
					$mail_from = 'no-reply@ogs-system.com.tw';
					$mail_to = $row["oa_email"];
					$mail_content = '<p>帳號: '.$row["oa_account"].'</p><p>您的新密碼: '.$rand_password.'</p>';
					$mail_subject = '取回您的帳號';
					$mail_name = 'OGS Admin system';
					
					CORE::mail_handle($mail_from,$mail_to,$mail_content,$mail_subject,$mail_name);
					
					$return_status = true;
				}else{
					$return_status = false;
				}
			}else{
				$return_status = false;
			}
			
			// 後處理
			if($return_status){
				CORE::notice('已經寄送帳號密碼至您的信箱',CORE::$manage,true);
			}
			
			if(!$return_status && is_array($args) && count($args) > 0){
				CORE::notice('錯誤的資訊',CORE::$manage.'forget/',true);
			}
		}

		// 後台管理首頁
		private static function index(){
			$temp_option = array(
				"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
				"MAIN" => self::$temp.'ogs-admin-index-tpl.html'
			);
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}
		
		// 簡易取得單項資料,並直接以資料欄位名稱輸出至 VIEW
		/*
		protected static function simple_load($tb_name,array $where,$no_output=false){
			foreach($where as $field => $value){
				$where_array[] = $field." = '".$value."'";
			}
			
			$where_str = implode(",",$where_array);

			$select = array (
				'table' => $tb_name,
				'field' => "*",
				'where' => $where_str,
				//'order' => '',
				'limit' => '0,1',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				if(!$no_output){
					foreach($row as $field => $value){
						VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
					}
				}
				
				return $row;
			}else{
				return false;
			}
		}
		*/
	}
?>