<?php

	class DOWNLOAD{
		
		//protected static $pointer; // 指定參數
		protected static $func; // 功能參數
		protected static $seo = false; // seo 判定參數
		
		function __construct($args){
					
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			switch(self::$func){
				default:
					$temp_option = $temp_option + array("MAIN" => 'ogs-download-tpl.html',"PAGE" => 'ogs-page-tpl.html');
					self::show();
				break;
			}
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		// 主分類列表
		public static function show(){
			$select = array(
				'table' => CORE::$config["prefix"].'_download',
				'field' => '*',
				'where' => "d_status = '1'",
				'order' => 'd_top desc, d_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);
		
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$lang.'download/');
			$rsnum = DB::num($sql);
			
			new SEO('download');
			$nav[0] = array('name' => 'Download','link' => false);
			BREAD::make($nav);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock("TAG_D_LIST");
					foreach($row as $field => $value){
						switch($field){
							case "d_hot":
								$value = (!empty($value))?'style="display: inline-block;"':'style="display: none;"';
							break;
						}
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
					
					VIEW::assign(array(
						"VALUE_D_IMG" => CRUD::img_handle($row["d_img"]),
						"VALUE_D_ROW" => ++$i,
					));
					
					if(!empty($row["d_file"])){
						$file_array = unserialize($row["d_file"]);
						unset($d);
						
						
						if(count($file_array) > 1){
							VIEW::newBlock("TAG_FILE_BLOCK");
						}
						
						unset($d,$f);
						$file_num = count($file_array) - 1;
						foreach($file_array as $file_name => $file_path){
							
							if(++$d > 1){
								VIEW::newBlock("TAG_FILE_LIST");
								VIEW::assign(array(
									"VALUE_D_FILE_NAME" => $file_name,
									"VALUE_D_FILE" => CRUD::img_handle($file_path),
								));
							}
							
							if($d == 1){
								VIEW::assignGlobal("VALUE_D_FILE_FIRST",CRUD::img_handle($file_path));
							}else{
								++$f;
							}
							
							if($f == ceil($file_num / 2) && $file_num > 2){
								VIEW::assign("TAG_DOWNLOAD_UL",'</ul><ul class="down-page">');
							}
						}
					}
				}
			}
		}

	}

?>