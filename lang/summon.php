<?php

	class LANG extends CORE{
		public static $id = 0;
		
		function __construct(){
			
			$self_path = CORE::real_path(__FILE__);
			
			// 讀取語言包
			$file_array = glob($self_path.'*.php');
			if(is_array($file_array) && count($file_array) > 1){
				foreach($file_array as $file_key => $file_path){
					if(!preg_match('/(summon.php)/',$file_path)){
						include_once $file_path;
					}
				}
			}
			
			self::switch_make();
		}
		
		// 取得最新語系 ID
		public static function lang_fetch(){
			// 取得所有資料表
			$sql = mysql_list_tables(CORE::$config["connect"]["db"],DB::$con);
			while($row = DB::fetch($sql)){
				$tb_name = $row["Tables_in_".CORE::$config["connect"]["db"]];
				
				// 檢查是否有 lang_id
				$select = array (
					'table' => $tb_name,
					'field' => "lang_id",
					//'where' => '',
					//'order' => '',
					//'limit' => '',
				);
				
				$sql_lang = DB::select($select);
				$rsnum = DB::num($sql_lang);
				
				if(!empty($rsnum)){
					while($lang_row = DB::fetch($sql_lang)){
						self::$id = (self::$id <= $lang_row["lang_id"])?$lang_row["lang_id"]:self::$id;
					}
				}
			}
		}
		
		// 同步儲存多語系
		public static function lang_sync($tb_name,array $new_args,array $args){
			
			if(CHECK::is_array_exist(CORE::$config["lang"]) && !empty($args["lang_sync"])){
				$nofix_tb_name = preg_replace("/".CORE::$config["prefix"]."/", '', $tb_name,1); // 資料表名稱去除 prefix
				
				// 取得其他語系資料庫 prefix
				foreach(CORE::$config["lang"] as $lang => $prefix){
					if($prefix != CORE::$config["prefix"]){
						$other_tb_name = $prefix.$nofix_tb_name;
						DB::insert($other_tb_name,$new_args);
					}
				}
			}
			
			CHECK::check_clear();
		}
		
		// 顯示語系切換按鍵
		public static function switch_make($lang_id=false){
			
			if(CHECK::is_array_exist(CORE::$config["lang"]) && count(CORE::$config["lang"]) > 1){
				foreach(CORE::$config["lang"] as $lang => $prefix){
					$i++;
					
					if($i == 1){
						$lang_link = 'http://'.CORE::$config["url"].CORE::$config["root"].CORE::$config["manage"];
					}else{
						$lang_link = 'http://'.CORE::$config["url"].CORE::$config["root"].$lang.'/'.CORE::$config["manage"];
					}
					
					if($lang == CORE::$config["langfix"]){
						$lang_link = '#';
					}
					
					if(!empty($lang_id)){
						self::link_sync($lang_id);
					}
					
					$lang_array[] = '<a href="'.$lang_link.'">'.$lang.'</a>';
				}
				
				if(is_array($lang_array)){
					VIEW::assignGlobal("TAG_LANG_SWITCH",implode("",$lang_array));
				}
			}
		}
		
		// 語系同頁切換功能
		private static function link_sync($lang_id){
			
			
		}
	}
?>