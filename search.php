<?php

	class SEARCH{
		
		private static $all_rsnum;
		
		private static $condition = array(
			'products' => array('p_name'),
			'news' => array('n_subject','n_content'),
			'download' => array('d_subject','d_file'),
		);
		
		function __construct($args=false){
			
			if($args[0] == "hot_search"){
				self::hot();
				exit;
			}
			
			CORE::res_init('tab','box');
			
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-left-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
				"PAGE" => 'ogs-page-tpl.html',
			);
			
			$kw = (!empty($_REQUEST["kw"]))?$_REQUEST["kw"]:$args[0];
			
			CHECK::is_must($kw);
			
			if(CHECK::is_pass()){
				$temp_main = array("MAIN" => 'ogs-search-tpl.html');
				self::search_handle($kw);
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
			
			$nav[0] = array("name" => 'Search');
			BREAD::make($nav);
			
			PRODUCTS::show(true);
			
			self::products($kw);
			self::news($kw);
			self::download($kw);
			
			if(empty(self::$all_rsnum)){
				VIEW::assignGlobal("TAG_SEARCH_NONE",'No results!');
			}
		}
		
		//----------------------------------------------------------------------------
		
		// 搜尋條件
		private static function search_condition($kw,$function){
			
			if(is_array(self::$condition[$function])){
				foreach(self::$condition[$function] as $st){
					switch($st){
						default:
							$where_array[] = $st." like '%".$kw."%'";
						break;
					}
				}
				
				if(is_array($where_array)){
					return implode(" and ",$where_array);
				}
			}
		}
		
		// 輸出參數處理
		private static function args_handle(array $row){
			foreach($row as $field => $value){
				switch($field){
					case "p_s_img":
						$value = CRUD::img_handle($value);
					break;
					case "n_hot":
					case "d_hot":
						$value = (!empty($value))?'style="display: inline-block;"':'style="display: none;"';
					break;
				}
				VIEW::assign("VALUE_".strtoupper($field),$value);
			}
		}
		
		// 搜尋產品
		private static function products($kw,$hot=false){
			
			$where = self::search_condition($kw,__FUNCTION__);
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products',
				'field' => '*',
				'where' => "p_status = '1' and ".$where,
				'order' => "p_name asc",
				//'limit' => "",
			);

			$sql = DB::select($select);
			$sql = PAGE::handle($sql,CORE::$lang.'search/'.$kw.'/');
			$rsnum = DB::num($sql);
			
			self::$all_rsnum = self::$all_rsnum + $rsnum; 
			
			if(!empty($rsnum)){
				if(!$hot){
					VIEW::newBlock("TAG_P_TITLE");
					VIEW::assign("VALUE_RS_NUM",$rsnum);
					
					VIEW::newBlock("TAG_P_BLOCK");
				}
				
				while($row = DB::fetch($sql)){
					if(!$hot){
						VIEW::newBlock("TAG_P_LIST");
						self::args_handle($row);
					}
					
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["p_id"];
					
					if(!$hot){
						VIEW::assign(array(
							"VALUE_P_LINK" => CORE::$lang.'products/detail/'.$pointer,
							"VALUE_ROW_NUM" => ++$i,
						));
					}else{
						$row["p_link"] = CORE::$lang.'products/detail/'.$pointer;
						$all_row[] = $row;
					}
				}
				
				if($hot){
					return $all_row;
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
			
			self::$all_rsnum = self::$all_rsnum + $rsnum;
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_N_TITLE");
				VIEW::assign("VALUE_RS_NUM",$rsnum);
				
				VIEW::newBlock("TAG_N_BLOCK");
				
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_N_LIST");
					self::args_handle($row);
					
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["n_id"];
					VIEW::assign(array(
						"VALUE_N_LINK" => CORE::$lang.'news/detail/'.$pointer,
						"VALUE_ROW_NUM" => ++$i,
					));
				}
			}
		}

		// 搜尋下載
		private static function download($kw){
			
			$where = self::search_condition($kw,__FUNCTION__);
			
			$select = array(
				'table' => CORE::$config["prefix"].'_download',
				'field' => '*',
				'where' => "d_status = '1' and ".$where,
				'order' => "d_subject asc",
				//'limit' => "",
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			self::$all_rsnum = self::$all_rsnum + $rsnum;
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_D_TITLE");
				VIEW::assign("VALUE_RS_NUM",$rsnum);
				
				VIEW::newBlock("TAG_D_BLOCK");
				
				while($row = DB::fetch($sql)){
					//VIEW::newBlock("TAG_D_LIST");
					//self::args_handle($row);
					
					if(!empty($row["d_file"])){
						$file_array = unserialize($row["d_file"]);
						foreach($file_array as $file_name => $file_path){
							VIEW::newBlock("TAG_D_LIST");
							VIEW::assign(array(
								"VALUE_D_FILE_NAME" => $file_name,
								"VALUE_D_FILE" => CRUD::img_handle($file_path),
								"VALUE_ROW_NUM" => ++$i,
							));
						}
					}
				}
			}
		}
		
		// 搜尋展覽
		private static function exhibition(){}
		
		// 快速提示
		private static function hot(){
			CHECK::is_must($_REQUEST["call"]);
			
			if(CHECK::is_pass()){
				$all_row = self::products($_REQUEST["call"],true);
				
				if(is_array($all_row)){
					foreach($all_row as $row){
						$hot_list_array[] = '<li><a href="'.$row["p_link"].'"><span><img src="'.$row["p_s_img"].'" /></span>'.$row["p_name"].'</a></li>';
					}
					
					$rs['done'] = 1;
					$rs['list'] = implode("",$hot_list_array);
					
					echo json_encode($rs);
					exit;
				}
			}
		}
	}
	

?>