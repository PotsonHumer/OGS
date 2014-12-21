<?php

	class EXHIBITION{

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
					NEWS::detail();
				break;
				default:
					$temp_option = $temp_option + array("MAIN" => 'ogs-exhibition-tpl.html',"PAGE" => 'ogs-page-tpl.html');
					NEWS::show(3);
				break;
			}
			
			self::clear_side(); // 顯示展覽時關閉側邊列表
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		// 顯示展覽列表
		public static function side_list(){
			$select = array(
				'table' => CORE::$config["prefix"].'_news',
				'field' => '*',
				'where' => "n_status = '1' and nc_id = '3'",
				'order' => 'n_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_SIDE_BLOCK");
				while($row = DB::fetch($sql)){
					new SEO($row["seo_id"],false);
					$link_pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row['n_id'];
					
					VIEW::newBlock("TAG_SIDE_LIST");
					VIEW::assign(array(
						"VALUE_N_SUBJECT" => $row["n_subject"],
						"VALUE_N_LINK" => CORE::$lang.'news/detail/'.$link_pointer,
						"VALUE_N_IMG" => CRUD::img_handle($row["n_img"]),
					));
				}
			}
		}
		
		// 顯示展覽時關閉側邊列表
		private static function clear_side(){
			if(CHECK::is_array_exist(VIEW::$parameter)){
				foreach(VIEW::$parameter as $sort => $type_array){
					if($type_array[1] == "TAG_SIDE_BLOCK"){
						unset(VIEW::$parameter[$sort],VIEW::$parameter[$sort][1]);
					}
				}
			}
		
			CHECK::check_clear();
		}
	}
?>