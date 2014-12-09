<?php

	class SEARCH{
		
		private static $condition = array(
			'products' => array('p_name'),
			'news' => array('n_subject','n_content'),
			'download' => array('d_subject'),
		);
		
		function __construct(){
			
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-left-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			CHECK::is_must($_REQUEST["kw"]);
			
			if(CHECK::is_pass()){
				$temp_main = array("MAIN" => 'ogs-search-tpl.html');
				self::search_handle($_REQUEST["kw"]);
			}else{
				$temp_main = array("MAIN" => 'ogs-msg-tpl.html');
				CORE::notice('Keyword missing...',CORE::$lang);
			}
			
			$temp_option = array_merge($temp_option,$temp_main);
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		//----------------------------------------------------------------------------
		
		// 處理搜尋功能
		private static function search_handle($kw){
			self::products($kw);
			self::news($kw);
		}
		
		//----------------------------------------------------------------------------
		
		// 搜尋條件
		private static function search_condition($kw,$function){
			
			if(is_array(self::$condition[$function])){
				foreach(self::$condition[$function] as $st){
					switch($st){
						default:
							$where_array[] = $st." = '%".$kw."%'";
						break;
					}
				}
				
				if(is_array($where_array)){
					return implode(" and ",$where_array);
				}
			}

		}
		
		// 搜尋產品
		private static function products($kw){
			
			$where = self::search_condition($kw,__FUNCTION__);
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products',
				'field' => '*',
				'where' => "p_status = '1' and ".$where,
				'order' => "p_name asc",
				//'limit' => "",
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_P_BLOCK");
				
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_P_LIST");
				}
			}
		}
		
		// 搜尋最新消息
		private static function news($kw){
			
			$where = self::search_condition($kw,__FUNCTION__);
			
			$select = array(
				'table' => CORE::$config["prefix"].'_news',
				'field' => '*',
				'where' => "n_status = '1' and nc_id = '2' and ".$where,
				'order' => "n_subject asc",
				//'limit' => "",
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_N_BLOCK");
				
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_N_LIST");
				}
			}
		}
		
		// 搜尋展覽
		
		// 搜尋下載
	}
	

?>