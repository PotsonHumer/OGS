<?php

	class PRODUCTS{
		
		protected static $pointer; // 指定參數
		protected static $func; // 功能參數
		protected static $seo = false; // seo 判定參數
		
		function __construct($args){
			
			self::$func = array_shift($args); // 取得功能參數
			
			self::$pointer = array_shift($args); // 頁面參數 , id or seo file name
			if(!is_numeric(self::$pointer) && !empty(self::$pointer)){
				self::$seo = true;
			}
			
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-left-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			switch(self::$func){
				case "detail":
					$temp_option = $temp_option + array("MAIN" => 'ogs-products-detail-tpl.html');
					CORE::res_init('jQueryAssets/jquery.ui.core.min','jQueryAssets/jquery.ui.theme.min','jQueryAssets/jquery.ui.tabs.min','css');
					CORE::res_init('jQueryAssets/jquery.ui-1.10.4.tabs.min','js');
					CORE::res_init('fix','box');
					self::detail();
				break;
				case "cate":
					$temp_option = $temp_option + array("MAIN" => 'ogs-products-cate-tpl.html');
					CORE::res_init('fix','box');
					//self::cate();
					self::level();
				break;
				default:
					$temp_option = $temp_option + array("MAIN" => 'ogs-products-tpl.html');
					self::show();
				break;
			}
			
			self::show(true);
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		// 主分類列表
		public static function show($left=false,$footer=false){
			$select = array(
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => '*',
				'where' => "pc_status = '1' and pc_parent = '0'",
				'order' => 'pc_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!$left){
				new SEO('products');
				
				$nav[0] = array('name' => 'Products','link' => false);
				BREAD::make($nav);
			}
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					new SEO($row["seo_id"],false);
					
					if($footer){
						VIEW::newBlock("TAG_PC_FOOTER");
					}else{
						if(!$left){
							VIEW::newBlock("TAG_PC_LIST");
						}else{
							VIEW::newBlock("TAG_PC_LEFT");
						}
					}
					
					foreach($row as $field => $value){
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
					
					$link_pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row['pc_id'];
					VIEW::assign("VALUE_PC_LINK",CORE::$lang.'products/cate/'.$link_pointer);
				}
			}
		}

		// 分類階層檢查
		private static function level(){
				
			$pc_row = self::pc_detail();
			self::pc_nav($pc_row);
			
			if(empty($pc_row["pc_parent"])){ // 轉跳至第一個次分類
				$select = array(
					'table' => CORE::$config["prefix"].'_products_cate',
					'field' => '*',
					'where' => "pc_status = '1' and pc_parent = '".$pc_row["pc_id"]."'",
					'order' => 'pc_sort '.CORE::$config["sort"],
					'limit' => '0,1',
				);

				$sql = DB::select($select);
				$rsnum = DB::num($sql);
				
				if(!empty($rsnum)){
					$row = DB::fetch($sql);
					
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["pc_id"];
					
					header("location: ".CORE::$lang.'products/cate/'.$pointer);
				}
				
				self::cate();
				
			}else{ // 顯示產品列表
				self::cate();
			}
		}
		
		
		// 第三層後分類與產品列表
		protected static function cate(){
			
			$pc_row = self::pc_detail();
			BREAD::fetch($pc_row);
			new SEO($pc_row["seo_id"]);
			
			if($pc_row["pc_custom_status"]){
				VIEW::assignGlobal("VALUE_PC_CUSTOM",$pc_row["pc_custom"]);
			}
			
			$cate_h1 = (!empty(SEO::$array["seo_h1"]))?SEO::$array["seo_h1"]:$pc_row["pc_name"];
			VIEW::assignGlobal("VALUE_H1",$cate_h1);

			$select = "SELECT p.*,pc.pc_id,pc.pc_name,pc.pc_img FROM ".CORE::$config["prefix"]."_products as p 
						LEFT JOIN ".CORE::$config["prefix"]."_products_cate as pc on pc.pc_id = p.pc_id 
						WHERE pc.pc_status = '1' and p.p_status = '1' and pc.pc_parent = '".$pc_row["pc_id"]."' 
						ORDER BY pc.pc_sort ".CORE::$config["sort"].", p.p_sort ".CORE::$config["sort"];
			
			$sql = DB::select(false,$select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum) && !$pc_row["pc_custom_status"]){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_P_LIST");
					foreach($row as $field => $value){
						switch($field){
							case "p_s_img":
								$value = (!empty($value))?$value:CORE::$config["noimg"];
							break;
						}
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
					
					if($last_pc_id != $row["pc_id"]){
						$pc_img_str = (!empty($row["pc_img"]))?'<img class="h2_img" src="'.CRUD::img_handle($row["pc_img"]).'">':'';
						//VIEW::assign("TAG_PC_NAME",'<h2>'.$pc_img_str.$row["pc_name"].'</h2>');
						VIEW::assign("TAG_PC_NAME",'<h2>　</h2>');
					}
					
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["p_id"];

					VIEW::assign("VALUE_P_LINK",CORE::$lang.'products/detail/'.$pointer);

					$last_pc_id = $row["pc_id"];
				}
			}
		}
		
		// 產品次分類選單
		private static function pc_nav(array $main_row){
				
			$top_id = self::pc_top($main_row["pc_id"]);
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => '*',
				'where' => "pc_status = '1' and pc_parent = '".$top_id."'",
				'order' => 'pc_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_PC_NAV");
				
				while($row = DB::fetch($sql)){
					
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["pc_id"];
					
					VIEW::newBlock("TAG_PC_NAV_LIST");
					VIEW::assign(array(
						"VALUE_PC_NAME" => (!empty(SEO::$array["seo_h1"]))?SEO::$array["seo_h1"]:$row["pc_name"],
						"VALUE_PC_LINK" => CORE::$lang.'products/cate/'.$pointer,
						"VALUE_PC_CURRENT" => ($pointer == self::$pointer)?'class="current"':'',
					));
				}
			}
		}

		// 產品詳細頁
		private static function detail(){

			if(self::$seo){
				$where = " and seo.seo_file_name = '".self::$pointer."'";
			}else{
				$where = " and p.p_id = '".self::$pointer."'";
			}
			
			$select = "SELECT * FROM ".CORE::$config["prefix"]."_products as p 
						LEFT JOIN ".CORE::$config["prefix"]."_seo as seo on seo.seo_id = p.seo_id 
						WHERE p.p_status = '1'".$where;

			$sql = DB::select(false,$select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				$p_h1 = (!empty($row["seo_h1"]))?$row["seo_h1"]:$row["p_name"];
				VIEW::assignGlobal(array(
					"VALUE_H1" => $p_h1,
					"TAG_BACK_LIST" => self::back_list($row["pc_id"]),
				));
				
				foreach($row as $field => $value){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				self::prev_next($row["p_id"],$row["pc_id"]);
				self::p_img($row["p_id"]);
				$desc_num = self::p_desc($row["p_id"]);
				self::p_relate($row["p_relate"],$desc_num);
				BREAD::fetch($row);
			}
		}
		
		// 取得上層分類資料
		private static function pc_detail(){
			
			if(self::$seo){
				$where = " and seo.seo_file_name = '".self::$pointer."'";
			}else{
				$where = " and pc.pc_id = '".self::$pointer."'";
			}
			
			$select = "SELECT * FROM ".CORE::$config["prefix"]."_products_cate as pc 
						LEFT JOIN ".CORE::$config["prefix"]."_seo as seo on seo.seo_id = pc.seo_id 
						WHERE pc.pc_status = '1' ".$where;
		
			$sql = DB::select(false,$select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum) && !empty(self::$pointer)){
				$row = DB::fetch($sql);
				return $row;
			}else{
				return false;
			}
		}
		
		// 取得頂層分類
		private static function pc_top($id){
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => '*',
				'where' => "pc_status = '1' and pc_id = '".$id."'",
				//'order' => "",
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				if(!empty($row["pc_parent"])){
					return self::pc_top($row["pc_parent"]);
				}else{
					return $row["pc_id"];
				}
			}
		}
		
		// 取得產品圖片
		private static function p_img($p_id){
			$select = array(
				'table' => CORE::$config["prefix"].'_products_img',
				'field' => '*',
				'where' => "p_id = '".$p_id."'",
				'order' => 'pi_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_P_IMG");
					VIEW::assign("VALUE_P_IMG",$row["pi_img"]);
				}
			}
		}
		
		// 取得產品描述
		private static function p_desc($p_id){
			$select = array(
				'table' => CORE::$config["prefix"].'_products_desc',
				'field' => '*',
				'where' => "p_id = '".$p_id."'",
				'order' => 'pd_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_PD_TITLE");
					VIEW::assign(array(
						"VALUE_PD_ROW" => ++$i,
						"VALUE_PD_TITLE" => $row["pd_title"],
					));
					
					VIEW::newBlock("TAG_PD_CONTENT");
					VIEW::assign(array(
						"VALUE_PD_ROW" => $i,
						"VALUE_PD_CONTENT" => CORE::content_handle($row["pd_content"],true),
					));
				}
			}
			
			return $i;
		}
		
		// 相關產品
		private static function p_relate($p_relate,$desc_num=1){
			if(!empty($p_relate)){
				$p_id_array = unserialize($p_relate);
				$p_id_str = "'".implode("','",$p_id_array)."'";
				
				$select = "SELECT *,p.seo_id FROM ".CORE::$config["prefix"]."_products as p 
							LEFT JOIN ".CORE::$config["prefix"]."_products_cate as pc on pc.pc_id = p.pc_id 
							WHERE pc.pc_status='1' and p.p_status='1' and p.p_id in(".$p_id_str.") ORDER BY pc.pc_parent asc,pc.pc_sort ".CORE::$config["sort"].",p.p_sort ".CORE::$config["sort"];
			
				$sql = DB::select(false,$select);
				$rsnum = DB::num($sql);
				
				if(!empty($rsnum)){
					VIEW::newBlock("TAG_RELATE_TITLE");
					VIEW::assign("VALUE_P_ROW",++$desc_num);
					
					VIEW::newBlock("TAG_RELATE_BLOCK");
					VIEW::assign("VALUE_P_ROW",$desc_num);
					
					while($row = DB::fetch($sql)){
						new SEO($row["seo_id"],false);
						$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["p_id"];
						
						VIEW::newBlock("TAG_RELATE_LIST");
						VIEW::assign(array(
							"VALUE_P_NAME" => $row["p_name"],
							"VALUE_P_IMG" => CRUD::img_handle($row["p_s_img"]),
							"VALUE_P_LINK" => CORE::$lang.'products/detail/'.$pointer,
						));
					}
				}
			}
		}
		
		// 取得回列表連結
		private static function back_list($pc_id){
			
			static $pc_layer;
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => '*',
				'where' => "pc_status = '1' and pc_id = '".$pc_id."'",
				//'order' => "",
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				if(!empty($row["pc_parent"])){
					$pc_layer[] = $row;
					return self::back_list($row["pc_parent"]);
				}else{
					$pc_row = array_pop($pc_layer);
					new SEO($pc_row,false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$pc_row["pc_id"];
					
					return CORE::$lang.'products/cate/'.$pointer;
				}
			}
		}
		
		// 產品上下頁
		private static function prev_next($p_id,$pc_id){
			
			$select = array(
				'table' => CORE::$config["prefix"].'_products',
				'field' => '*',
				'where' => "p_status = '1' and pc_id = '".$pc_id."'",
				'order' => "p_sort ".CORE::$config["sort"],
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					new SEO($row,false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["p_id"];
					$p_link = CORE::$lang.'products/detail/'.$pointer;
					
					if($next){
						VIEW::assignGlobal('VALUE_NEXT_LINK','<a class="btn" href="'.$p_link.'">Next</a>');
						unset($next);
					}
					
					if($row["p_id"] == $p_id){
						if(!empty($last_link)){
							VIEW::assignGlobal('VALUE_PREV_LINK','<a class="btn" href="'.$last_link.'">Previous</a>');
						}
						
						$next = true;
					}
					
					$last_link = $p_link;
				}
			}
		}
	}
	

?>