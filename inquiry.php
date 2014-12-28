<?php

	class INQUIRY{
		
		private static $pointer; // 指定參數
		private static $func; // 指定功能
		
		function __construct($args){
			
			self::$func = array_shift($args); // 取得功能參數
			self::$pointer = array_shift($args); // 頁面參數 , id or seo file name
			
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-left-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			switch(self::$func){
				case "add":
					self::add(self::$pointer);
				break;
				case "del":
					self::del(self::$pointer);
				break;
				case "num":
					self::num(self::$pointer);
				break;
				case "send":
					$temp_main = array("MAIN" => 'ogs-msg-tpl.html');
					self::send();
				break;
				default:
					CORE::res_init('get','box');
					PRODUCTS::show(true);
					self::form();
					$temp_main = array("MAIN" => 'ogs-inquiry-tpl.html');
				break;
			}
			
			$nav[0] = array('name' => 'Inquiry');
			BREAD::make($nav);
			
			$temp_option = array_merge($temp_option,$temp_main);
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		// 增加詢價產品
		public static function add($id=false){
			
			if(CHECK::is_array_exist($_REQUEST["id"])){
				$id_str = "'".implode("','",$_REQUEST["id"])."'";
				$where = " and p_id in(".$id_str.")";
			}
			
			if(!empty($id)){
				$where = " and p_id = '".$id."'";
			}

			CHECK::check_clear();
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products',
				'field' => '*',
				'where' => "p_status = '1'".$where,
				//'order' => "",
				//'limit' => "",
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum) && !empty($where)){
				while($row = DB::fetch($sql)){
					$_SESSION[CORE::$config["sess"]]["inquiry"][$row["p_id"]] = $_SESSION[CORE::$config["sess"]]["inquiry"][$row["p_id"]] + 1;
				}
			}
			
			header("location: ".CORE::$lang.'inquiry/');
			exit;
		}
		
		// 顯示詢價產品 & 表單
		private static function form(){
			self::refill();
			CORE::country_select();
			
			if(CHECK::is_array_exist($_SESSION[CORE::$config["sess"]]["inquiry"])){
				self::p_load();
			}else{
				CORE::notice('Inquiry cart empty!',CORE::$lang,true);
			}
			
			CHECK::check_clear();
		}
		
		// 詢價產品列表
		private static function p_load(){
			foreach($_SESSION[CORE::$config["sess"]]["inquiry"] as $id => $num){
				
				$select = array(
					'table' => CORE::$config["prefix"].'_products',
					'field' => 'p_id,p_name,p_s_img',
					'where' => "p_status = '1' and p_id = '".$id."'",
					//'order' => "",
					//'limit' => "",
				);
			
				$sql = DB::select($select);
				$rsnum = DB::num($sql);
				
				if(!empty($rsnum)){
					$row = DB::fetch($sql);
					
					$p_img = 'http://'.CORE::$config['url'].CRUD::img_handle($row["p_s_img"]);
					$p_img = preg_replace('/ /','%20',$p_img);
					
					VIEW::newBlock("TAG_INQUIRY_LIST");
					VIEW::assign(array(
						"VALUE_P_ROW" => ++$i,
						"VALUE_P_NUM" => $num,
						"VALUE_P_ID" => $row["p_id"],
						"VALUE_P_NAME" => $row["p_name"],
						"VALUE_P_S_IMG" => $p_img,
						"VALUE_P_DELETE" => CORE::$lang.'inquiry/del/'.$id.'/'
					));
				}
			}
		}
		
		// 修改詢價數量
		private static function num(){
			if(is_numeric($_REQUEST["call"]) && $_REQUEST["call"] > 0 && !empty($_SESSION[CORE::$config["sess"]]["inquiry"][self::$pointer])){
				$_SESSION[CORE::$config["sess"]]["inquiry"][self::$pointer] = ceil($_REQUEST["call"]);
			}
			
			exit;
		}
		
		// 刪除詢價產品
		private static function del($id=false){
			unset($_SESSION[CORE::$config["sess"]]["inquiry"][$id]);
			
			header("location: ".CORE::$lang.'inquiry/');
			exit;
		}
		
		// 儲存詢價
		private static function send(){
			CHECK::is_must($_REQUEST["iq_company"],$_REQUEST["iq_name"],$_REQUEST["iq_city"],$_REQUEST["iq_tel"],$_REQUEST["iq_cellphone"],$_REQUEST["iq_content"]);
			CHECK::is_array_exist($_SESSION[CORE::$config["sess"]]["inquiry"]);
			CHECK::is_email($_REQUEST["iq_email"]);
			
			if(CHECK::is_pass()){
				$input = $_REQUEST;
				
				$input["iq_content"] = addslashes(htmlspecialchars($input["iq_content"]));
				$match_input = CRUD::field_match('ogs_inquiry',$input);
				
				DB::insert('ogs_inquiry',$match_input);
				$iq_id = DB::get_id();
				
				// 儲存詢價產品
				foreach($_SESSION[CORE::$config["sess"]]["inquiry"] as $id => $num){
					unset($iqi_input);
					
					$iqi_input = array(
						"iq_id" => $iq_id,
						"p_id" => $id,
						"iqi_num" => $num,
						"iqi_lang" => CORE::$config["prefix"],
					);

					DB::insert('ogs_inquiry_items',$iqi_input);
				}
				
				CHECK::check_clear();
				
				// MAIL
				self::mail_handle($match_input);
				
				unset($_SESSION[CORE::$config["sess"]]["inquiry"]);
				CORE::notice('Data has submitted',CORE::$lang);
			}else{
				self::refill('in');
				CORE::notice('Missing some Data...',CORE::$lang.'inquiry/');
			}
		}
		
		// 回填表單
		private static function refill($func='out'){
			if(CHECK::is_array_exist($_REQUEST) && $func == 'in'){
				foreach($_REQUEST as $field => $value){
					$_SESSION[CORE::$config["sess"]]["iq_fill"][$field] = $value;
				}
			}

			if(CHECK::is_array_exist($_SESSION[CORE::$config["sess"]]["iq_fill"]) && $func == 'out'){
				foreach($_SESSION[CORE::$config["sess"]]["iq_fill"] as $field => $value){
					VIEW::assignGlobal('VALUE_'.strtoupper($field),$value);
				}
				
				unset($_SESSION[CORE::$config["sess"]]["iq_fill"]);
			}

			CHECK::check_clear();
		}
		
		// E-mail 處理
		private static function mail_handle(array $input){
			
			$mail_from = 'no-reply@ogs-system.com.tw';
			$mail_to = $_SESSION[CORE::$config["sess"]]["system"]["sys_email"];
			$mail_subject = '詢價發送通知 '.date("Y/m/d H:i:s");
			$mail_name = 'OGS Admin system';
			
			self::p_load();
			
			foreach($input as $field => $value){
				VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
			}
			
			$temp_parameter = VIEW::$parameter;
			new VIEW("ogs-inquiry-mail-tpl.html",false,true,false);
			VIEW::$parameter = $temp_parameter;
			
			CORE::mail_handle($mail_from, $mail_to, VIEW::$output, $mail_subject, $mail_name);
		}
	}

?>