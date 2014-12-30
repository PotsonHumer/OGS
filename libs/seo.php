<?php

	class SEO{
		
		public static $array;

		// 讀取 seo (實體化)
		function __construct($args,$tpl=true){
			
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
				
				if($tpl){
					foreach($row as $field => $value){
						VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
					}
				}
				
				self::$array = $row;
				
				return true;
			}else{
				self::$array = false;
				
				return false;
			}
		}
		
		// 儲存 seo
		public static function save($tb_seo,array $args,$tb_name=false,$table_prefix=false){
			
			if(CHECK::is_must($args["seo_id"])){
				$args["seo_short_desc"] = CORE::content_handle($args["seo_short_desc"]);
				$args["seo_file_name"] = self::seo_file_name_check($args["seo_file_name"],$args["seo_id"]);
				CRUD::U(CORE::$config["prefix"].'_seo',$args);
				$return_args = true;
			}else{
				if(!CHECK::is_must($args[$table_prefix."_id"])){
					$args[$table_prefix."_id"] = CRUD::$insert_id;
				}
				
				$new_args = CRUD::field_match(CORE::$config["prefix"].'_seo',$args);
				$new_args["seo_short_desc"] = CORE::content_handle($new_args["seo_short_desc"]);
				$new_args["seo_file_name"] = self::seo_file_name_check($new_args["seo_file_name"]);
				DB::insert($tb_seo,$new_args);
				//CRUD::C(CORE::$config["prefix"].'_seo',$args);

				// seo_id 回存原資料表
				if(CHECK::is_must($tb_name,$table_prefix,$args[$table_prefix."_id"])){
					$input["seo_id"] = DB::get_id();
					$input[$table_prefix."_id"] = $args[$table_prefix."_id"];
					
					CRUD::U($tb_name,$input);
				}
				$return_args = true;
			}
			
			CHECK::check_clear();
			
			if($return_args){
				return true;
			}else{
				return false;
			}
		}
		
		// 檢查 seo_file_name 有無重複
		private static function seo_file_name_check($seo_file_name,$seo_id=false){
			
			$where = (!empty($seo_id))?" and seo_id != '".$seo_id."'":'';
			
			$select = array(
				'table' => CORE::$config["prefix"].'_seo',
				'field' => "seo_file_name",
				"where" => "seo_file_name != '' and seo_file_name = '".$seo_file_name."'".$where,
				//"order" => "",
				//"limit" => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				return $seo_file_name.'-repeat';
			}else{
				return $seo_file_name;
			}
		}
		
		// 刪除 seo
		public static function del($tb_name,$field,$args){
			
			// 取得 seo_id
			if(CHECK::is_array_exist($args)){
				$args_str = "'".implode("','",$args)."'";
				$where = $field." in(".$args_str.")";
			}else{
				$where = $field."='".$args."'";
			}
			
			CHECK::check_clear();
			
			$select = array(
				'table' => $tb_name,
				'field' => "seo_id",
				"where" => $where,
				//"order" => "",
				//"limit" => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					DB::delete(CORE::$config["prefix"].'_seo',array('seo_id' => $row['seo_id'])); // 實際刪除 seo
					if(!empty(DB::$error)){
						CORE::notice(DB::$error,false,true);
						return false;
					}
				}
			}else{
				return false;
			}
		}
	}

?>