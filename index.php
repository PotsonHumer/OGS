<?php 
	
	class INDEX extends CORE{
		function __construct(){
			
			$temp_option = array(
				"HEADER" => 'ogs-index-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			CORE::res_init('super_slide','fix','box');
			new SEO('index');
			
			self::news_list();
			self::download_list();
			self::new_products_list();
			
			new VIEW("ogs-index-tpl.html",$temp_option,false,false);
		}
		
		// 首頁 最新消息列表
		private static function news_list(){
			NEWS::show();
		}
		
		// 首頁 檔案下載列表
		private static function download_list(){
			$select = array(
				'table' => CORE::$config["prefix"].'_download',
				'field' => '*',
				'where' => "d_status = '1'",
				'order' => 'd_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					if(!empty($row["d_file"])){
						//VIEW::newBlock("TAG_FILE_BLOCK");
						
						$file_array = unserialize($row["d_file"]);
						foreach($file_array as $file_name => $file_path){
							VIEW::newBlock("TAG_FILE_LIST");
							VIEW::assign(array(
								"VALUE_D_HOT" => (!empty($row["d_hot"]))?'style="display: inline-block;"':'style="display: none;"',
								"VALUE_D_FILE_NAME" => $row["d_subject"].' - '.$file_name,
								"VALUE_D_FILE" => CRUD::img_handle($file_path),
							));
						}
					}
				}
			}
		}
		
		// 首頁 最新產品列表
		private static function new_products_list(){
			$select = array(
				'table' => CORE::$config["prefix"].'_products',
				'field' => '*',
				'where' => "p_status = '1' and p_new = '1'",
				'order' => "rand()",
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock("TAG_P_LIST");
					foreach($row as $field => $value){
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
										
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["p_id"];

					VIEW::assign("VALUE_P_LINK",CORE::$lang.'products/detail/'.$pointer);
				}
			}
		}
	}
	
?>