<?php

	class SEO{

		// 讀取 seo (實體化)
		function __construct($args){
			
			if(CHECK::is_number($args)){
				$where = "seo_id = '".$args."'";
			}else{
				$where = "seo_name = '".$args."'";
			}
			
			CHECK::check_clear();
			
			$select = array(
				'table' => CORE::$config["prefix"].'_seo',
				'field' => "*",
				"where" => $where,
				//"order" => "",
				//"limit" => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				foreach($row as $field => $value){
					VIEW::assignGlobal("TAG_".strtoupper($field),$value);
				}
			}
		}
		
		// 儲存 seo
		public static function save(array $args,$tb_name=false,$input=false){
			if(CHECK::is_must($args["seo_id"])){
				CRUD::U(CORE::$config["preifx"].'_seo',$args);
				return true;
			}else{
				CRUD::C(CORE::$config["preifx"].'_seo',$args);
				
				// seo_id 回存原資料表
				if(CHECK::is_must($tb_name) && CHECK::is_array_exist($input)){
					$input["seo_id"] = DB::get_id();
					CRUD::U(CORE::$config["preifx"].'_seo',$input);
				}
				return true;
			}
			
			CHECK::check_clear();
			return false;
		}
	}

?>