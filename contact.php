<?php

	class CONTACT{
		
		//protected static $pointer;
		protected static $addon_checkbox = array(
			'ct_type' => array('Manufacturer','Trading Company','Agent / Distributor','User'),
			'ct_quest' => array('Please Provide Quotation','Request Catalog','More Product Information'),
			'ct_contact' => array('Phone','Fax','E-Mail')
		);
		
		protected static $func; // 功能參數
		
		function __construct($args){
			
			self::$func = array_shift($args); // 功能參數
			
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			switch(self::$func){
				case "add":
					$temp_main = array("MAIN" => 'ogs-msg-tpl.html');
					$temp_option = array_merge($temp_option,$temp_main);
					
					self::add();
				break;
				default:
					$temp_main = array("MAIN" => 'ogs-contact-tpl.html');
					$temp_option = array_merge($temp_option,$temp_main);
					
					self::show();
				break;
			}
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		protected static function show(){
			self::refill();
			self::addon_checkbox();
			CORE::country_select();
			
			$nav[] = array('name' => 'Contact Us');
			BREAD::make($nav);
			new SEO('contact');
		}
		
		// 紀錄表單
		protected static function add(){
			CHECK::is_must($_REQUEST["ct_company"],$_REQUEST["ct_name"],$_REQUEST["ct_city"],$_REQUEST["ct_zip"],$_REQUEST["ct_tel"],$_REQUEST["ct_cellphone"],$_REQUEST["ct_email"],$_REQUEST["ct_content"]);
			
			if(CHECK::is_pass()){
				$input = $_REQUEST;
				
				// 處理自訂增加 checkbox
				if(CHECK::is_array_exist(self::$addon_checkbox)){
					foreach(self::$addon_checkbox as $checkbox_name => $checkbox_array){
						if(CHECK::is_array_exist($input[$checkbox_name])){
							$addon[$checkbox_name] = implode(",",$input[$checkbox_name]);
							unset($input[$checkbox_name]);
						}
					}
				}
				
				if(CHECK::is_array_exist($addon)){
					$input = array_merge($input,$addon);
				}
				
				$input["ct_content"] = addslashes(htmlspecialchars($input["ct_content"]));
				$match_input = CRUD::field_match('ogs_contact',$input);
				
				DB::insert('ogs_contact',$match_input);
				CHECK::check_clear();
				
				// MAIL
				self::mail_handle($match_input);
				
				CORE::notice('Data has submitted',CORE::$lang);
			}else{
				self::refill('in');
				CORE::notice('Missing some Data...',CORE::$lang.'contact/');
			}
		}
		
		// 自訂增加 checkbox
		private static function addon_checkbox(){
			if(CHECK::is_array_exist(self::$addon_checkbox)){
				foreach(self::$addon_checkbox as $checkbox_name => $checkbox_array){
					
					foreach($checkbox_array as $key => $value){
						VIEW::newBlock('TAG_'.strtoupper($checkbox_name));
						VIEW::assign(array(
							"VALUE_CHECKBOX_ROW" => $key,
							"VALUE_CHECKBOX_ADDON" => $value,
							"VALUE_CHECKBOX_NAME" => $checkbox_name,
							"VALUE_CHECKBOX_CURRENT" => (CHECK::is_must($_REQUEST[$checkbox_name][$key]))?'checked':'',
						));
					}
				}
			}
			
			CHECK::check_clear();
		}
		
		// 回填表單
		private static function refill($func='out'){
			if(CHECK::is_array_exist($_REQUEST) && $func == 'in'){
				foreach($_REQUEST as $field => $value){
					$_SESSION[CORE::$config["sess"]]["ct_fill"][$field] = $value;
				}
			}

			if(CHECK::is_array_exist($_SESSION[CORE::$config["sess"]]["ct_fill"]) && $func == 'out'){
				foreach($_SESSION[CORE::$config["sess"]]["ct_fill"] as $field => $value){
					VIEW::assignGlobal('VALUE_'.strtoupper($field),$value);
				}
				
				unset($_SESSION[CORE::$config["sess"]]["ct_fill"]);
			}

			CHECK::check_clear();
		}
		
		// E-mail 處理
		private static function mail_handle(array $input){
			
			$mail_from = 'no-reply@ogs-system.com.tw';
			$mail_to = $_SESSION[CORE::$config["sess"]]["system"]["sys_email"];
			$mail_subject = '聯絡我們發送通知';
			$mail_name = 'OGS Admin system';
			
			foreach($input as $field => $value){
				VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
			}
			
			$temp_parameter = VIEW::$parameter;
			new VIEW("ogs-mail-tpl.html",false,true,false);
			VIEW::$parameter = $temp_parameter;
			
			CORE::mail_handle($mail_from, $mail_to, VIEW::$output, $mail_subject, $mail_name);
		}
	}
	

?>