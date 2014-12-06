<?php

	// 基本設定管理
	class SYSTEM extends OGSADMIN{
		static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "replace":
					$temp_option = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::replace();
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-system-tpl.html');
					$temp_option = array_merge($temp_option,$temp_main);
					self::show();
				break;
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}
		
		// 基本設定顯示
		private static function show(){
			$select = array(
				'table' => 'ogs_system',
				'field' => "*",
				'where' => "sys_id='1'",
				//'order' => "",
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				foreach($row as $field => $value){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
			}
			
			LANG::switch_make(false,'system/');
			self::seo_row();
		}
		
		// 儲存
		private static function replace(){
			CHECK::is_must($_REQUEST["sys_id"]);
			CHECK::is_email($_REQUEST["sys_email"]);
			CHECK::is_array_exist($_REQUEST["seo_id"]);
			
			if(CHECK::is_pass()){
				CRUD::U('ogs_system',$_REQUEST);
				
				$seo_field_array = array('seo_id','seo_name','seo_title','seo_keyword','seo_desc','seo_h1','seo_short_desc');
				foreach($_REQUEST["seo_id"] as $key => $seo_id){
					unset($input);
					foreach($seo_field_array as $field){
						$input[$field] = $_REQUEST[$field][$key];
					}
					
					CRUD::U(CORE::$config["prefix"].'_seo',$input);
				}

				CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 功能起始頁 SEO
		private static function seo_row(){			
			$select = array(
				'table' => CORE::$config["prefix"].'_seo',
				'field' => "*",
				'where' => "seo_name != ''",
				'order' => 'seo_id '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_SEO_BTN");
					VIEW::assign("MSG_SEO_NAME",LANG::$manage[$row["seo_name"]]);
					
					VIEW::newBlock("TAG_SEO_FUNC");
					VIEW::assign("MSG_SEO_NAME",LANG::$manage[$row["seo_name"]]);
					foreach($row as $field => $value){
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
				}
			}
		}
		
	}
	

?>