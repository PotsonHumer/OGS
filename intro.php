<?php

	class INTRO{
		
		protected static $pointer;
		
		function __construct($args){
			
			self::$pointer = array_shift($args); // 頁面參數 , id or seo file name
			if(!is_numeric(self::$pointer) && !empty(self::$pointer)){
				$seo = true;
			}
			
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"MAIN" => 'ogs-intro-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			self::show($seo);
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
		
		protected static function show($seo=false){
			
			if(!empty(self::$pointer)){
				if($seo){
					$where = "it.it_status = '1' and seo.seo_file_name = '".self::$pointer."'";
				}else{
					$where = " and it_id = '".self::$pointer."'";
				}
			}
			
			if($seo){
				$select = "SELECT * FROM ".CORE::$config["prefix"]."_intro as it 
							left join ".CORE::$config["prefix"]."_seo as seo on seo.seo_id = it.seo_id 
							WHERE ".$where." LIMIT 0,1";

				$sql = DB::select(false,$select);
			}else{
				$select = array(
					'table' => CORE::$config["prefix"].'_intro',
					'field' => '*',
					'where' => "it_status = '1'".$where,
					//'order' => 'it_sort '.CORE::$config["sort"],
					'limit' => '0,1',
				);
			
				$sql = DB::select($select);
			}
			
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				new SEO($row["seo_id"]);
				BREAD::fetch($row);
				
				foreach($row as $field => $value){
					switch($field){
						case "it_content":
							$value = CORE::content_handle($value,true);
						break;
						case "it_subject":
							$value = (!empty(SEO::$array["seo_h1"]))?SEO::$array["seo_h1"]:$value;
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
			}else{
				// 無資料	
			}
		}
		
		// 選單
		public static function submenu(){
			$select = array(
				'table' => CORE::$config["prefix"].'_intro',
				'field' => '*',
				'where' => "it_status = '1'",
				'order' => 'it_sort '.CORE::$config["sort"],
				//'limit' => '0,1',
			);

			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				VIEW::newBlock("TAG_IT_SUBMENU");
				while($row = DB::fetch($sql)){
					$i++;
					
					new SEO($row["seo_id"],false);
					$pointer = (!empty(SEO::$array["seo_file_name"]))?SEO::$array["seo_file_name"]:$row["it_id"];
					
					VIEW::newBlock("TAG_IT_LIST");
					VIEW::assign(array(
						"VALUE_IT_SUBJECT" => $row["it_subject"],
						"VALUE_IT_LINK" => ($i==1)?CORE::$lang.strtolower(__CLASS__).'/':CORE::$lang.strtolower(__CLASS__).'/'.$pointer,
					));
				}
			}
		}
	}
	

?>