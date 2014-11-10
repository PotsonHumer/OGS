<?php

	class LANG extends CORE{
		public static $id = 0;
		public static $manage;
		public static $msg;
		
		function __construct(){
			
			$self_path = CORE::real_path(__FILE__);
			
			// 讀取語言包
			self::$manage = include_once $self_path.'lang-cht.php';
			self::$msg = include_once $self_path.'lang-'.CORE::$config["langfix"].'.php';
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
				
				// 取得目前uri (去除語系與後台目錄)
				$manage_dir = preg_replace("/\//",'',CORE::$config["manage"]);
				$now_path = preg_replace("/([^>]*)".$manage_dir."\//",'',CORE::$path);
				
				foreach(CORE::$config["lang"] as $lang => $prefix){
					$i++;

					if(!empty($lang_id)){
						$now_path = self::link_sync($lang_id,$prefix,$now_path);
					}
					
					if($i == 1){
						$lang_link = 'http://'.CORE::$config["url"].CORE::$config["root"].CORE::$config["manage"].$now_path;
					}else{
						$lang_link = 'http://'.CORE::$config["url"].CORE::$config["root"].$lang.'/'.CORE::$config["manage"].$now_path;
					}
					
					if($lang == CORE::$config["langfix"]){
						$lang_link = '#';
						$lang_current = 'green';
					}else{
						$lang_current = '';
					}
										
					$lang_array[] = '<a class="span '.$lang_current.'" href="'.$lang_link.'">'.self::$manage[$lang].'</a>';
				}
				
				if(is_array($lang_array)){
					VIEW::assignGlobal("TAG_LANG_SWITCH",implode("",$lang_array));
				}
			}
		}
		
		// 語系同頁切換功能
		private static function link_sync($lang_id,$prefix,$now_path){
			
			$origin_id = self::origin_id($lang_id,$prefix); // 取得原始 id
			$now_path_array = explode("/",$now_path); // 拆解 uri
			
			if(CHECK::is_array_exist($now_path_array) && !empty($origin_id)){
				CHECK::check_clear();
				$now_path_array[(count($now_path_array) - 2)] = $origin_id;
				return implode("/",$now_path_array); // 重組 uri
			}
			
			CHECK::check_clear();
			return false;
		}
		
		// 取得 lang_id 項目的原始 id
		private static function origin_id($lang_id,$prefix){
			
			$sql = mysql_list_tables(CORE::$config["connect"]["db"],DB::$con);
			while($row = DB::fetch($sql)){
				$tb_name = $row["Tables_in_".CORE::$config["connect"]["db"]];
				$tb_match = preg_match("/".$prefix."_"."/",$tb_name);				
				
				// 檢查原始 id
				if($tb_match){
					$select = array (
						'table' => $tb_name,
						'field' => "*",
						'where' => "lang_id = '".$lang_id."'",
						//'order' => '',
						//'limit' => '',
					);
					
					$sql_lang = DB::select($select);
					$rsnum = DB::num($sql_lang);
					
					if(!empty($rsnum)){
						$lang_row = DB::fetch($sql_lang,true);
						return $lang_row[0];
					}else{
						return false;
					}
				}
			}
		}
	}
?>