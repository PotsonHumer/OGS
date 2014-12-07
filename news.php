<?php

	class NEWS{
		
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
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			switch(self::$func){
				case "detail":
					$temp_option = $temp_option + array("MAIN" => 'ogs-news-detail-tpl.html');
					self::detail();
				break;
				default:
					$temp_option = $temp_option + array("MAIN" => 'ogs-news-tpl.html',"PAGE" => 'ogs-page-tpl.html');
					self::show();
				break;
			}
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		// 主分類列表
		public static function show($cate=2){
			$select = array(
				'table' => CORE::$config["prefix"].'_news',
				'field' => '*',
				'where' => "n_status = '1' and nc_id = '".$cate."'",
				'order' => 'n_showdate desc,n_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$lang.'news/');
			$rsnum = DB::num($sql);
			
			new SEO('news');
			$nav[0] = array('name' => 'News','link' => false);
			BREAD::make($nav);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock("TAG_N_LIST");
					foreach($row as $field => $value){
						switch($field){
							case "n_img":
								$value = CRUD::img_handle($value);
							break;
						}
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
					
					new SEO($row["seo_id"],false);
					$link_pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row['n_id'];
					VIEW::assign(array(
						"VALUE_N_LINK" => CORE::$lang.'news/detail/'.$link_pointer,
						"VALUE_N_ROW" => ++$i,
						"VALUE_N_HOT" => (strtotime(date("Y-m-d")) - strtotime($row["n_showdate"]) <= (2 * 24 * 60 * 60))?'style="display: inline-block"':''
					));
				}
			}
		}

		// 產品詳細頁
		public static function detail(){

			if(self::$seo){
				$where = " and seo.seo_file_name = '".self::$pointer."'";
			}else{
				$where = " and n.n_id = '".self::$pointer."'";
			}
			
			$select = "SELECT * FROM ".CORE::$config["prefix"]."_news as n 
						LEFT JOIN ".CORE::$config["prefix"]."_seo as seo on seo.seo_id = n.seo_id 
						WHERE n.n_status = '1'".$where;

			$sql = DB::select(false,$select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				$nav[0] = array('name' => 'News','link' => CORE::$lang.'news/');
				$nav[1] = array('name' => $row["n_subject"],'link' => false);
				BREAD::make($nav);
				
				foreach($row as $field => $value){
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
			}
		}
		
		// 取得上層分類資料
		/*
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
				return DB::fetch($sql);
			}else{
				return false;
			}
		}
		*/
	}
	

?>