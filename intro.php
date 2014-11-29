<?php

	class INTRO{
		
		protected static $pointer;
		
		function __construct($args){
			
			self::$pointer = array_shift($args); // 頁面參數 , id or seo file name 
			if(!CHECK::is_number(self::$pointer) && !empty(self::$pointer)){
				$seo = true;
			}
			CHECK::check_clear();
			
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
					$where = "it_status = '1' and it_id = '".self::$pointer."'";
				}
			}
			
			if($seo){
				$select = "SELECT * FROM ".CORE::$config["prefix"]."_intro as it 
							left join ".CORE::$config["prefix"]."_seo as seo on seo.lang_id = it.lang_id
							where ".$where." limit 0,1";

				$sql = DB::select(false,$select);
			}else{
				$select = array(
					'table' => CORE::$config["prefix"].'_intro',
					'field' => '*',
					'where' => $where,
					//'order' => 'it_sort '.CORE::$config["sort"],
					'limit' => '0,1',
				);
			
				$sql = DB::select($select);
			}
			
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					if($field == "it_content"){
						$value = stripslashes($value);
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
			}else{
				// 無資料	
			}
		}
	}
	

?>