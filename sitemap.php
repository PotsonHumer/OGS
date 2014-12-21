<?php

	class SITEMAP{
		function __construct(){
			
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-left-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			PRODUCTS::show(true); // 左邊選單
			
			$temp_main = array('MAIN' => 'ogs-sitemap-tpl.html');
			$temp_option = array_merge($temp_option,$temp_main);
			
			self::show();
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		private static function show(){
			self::it_map();
			self::pc_map();
		}
		
		// 介紹頁
		private static function it_map(){
			$select = array(
				'table' => CORE::$config["prefix"].'_intro',
				'field' => '*',
				'where' => "it_status = '1'",
				'order' => "it_sort ".CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_IT_MAP");
				
				while($row = DB::fetch($sql)){
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["it_id"];
					
					VIEW::newBlock("TAG_IT_LIST");
					VIEW::assign(array(
						"VALUE_IT_SUBJECT" => $row["it_subject"],
						"VALUE_IT_LINK" => CORE::$lang.'intro/'.$pointer,
					));
				}
			}
		}

		// 產品分類
		private static function pc_map($pc_id=false){
			
			if(!empty($pc_id)){
				$where = " and pc_parent = '".$pc_id."'";
			}else{
				$where = " and pc_parent = '0'";
			}
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => '*',
				'where' => "pc_status = '1'".$where,
				'order' => "pc_sort ".CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				if(empty($pc_id)){
					VIEW::newBlock("TAG_PC_MAP");
				}else{
					VIEW::newBlock("TAG_PC_SUB_MAP");
				}
				
				while($row = DB::fetch($sql)){
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["pc_id"];
					
					if(empty($pc_id)){
						VIEW::newBlock("TAG_PC_LIST");
					}else{
						VIEW::newBlock("TAG_PC_SUB_LIST");
						self::p_map($row["pc_id"]);
						VIEW::gotoBlock("TAG_PC_SUB_LIST");
					}
					
					VIEW::assign(array(
						"VALUE_PC_NAME" => $row["pc_name"],
						"VALUE_PC_LINK" => CORE::$lang.'products/cate/'.$pointer,
					));
					
					self::pc_map($row["pc_id"]);
				}
			}
		}

		// 產品
		private static function p_map($pc_id){
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products',
				'field' => '*',
				'where' => "p_status = '1' and pc_id = '".$pc_id."'",
				'order' => "p_sort ".CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["p_id"];
					
					VIEW::newBlock("TAG_P_MAP");
					VIEW::assign(array(
						"VALUE_P_NAME" => $row["p_name"],
						"VALUE_P_LINK" => CORE::$lang.'products/detail/'.$pointer,
					));
				}
			}
		}
		
	}

?>