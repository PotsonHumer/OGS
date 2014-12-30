<?php

	class SORT{
		
		public static $rs = true;
		
		// 自動排序
		function __construct($tb_name,$field_prefix,$id,$sort){
			CHECK::is_array_exist($id);
			CHECK::is_array_exist($sort);
			
			$type = CHECK::is_pass();
			
			if(!$type){
				$id = (empty($id))?CRUD::$insert_id:$id;
				
				CHECK::is_must($id,$sort);
				if(!CHECK::is_pass()){
					CORE::notice('請選擇項目',$_SESSION[CORE::$config["sess"]]['last_path'],true);
					self::$rs = false;
				}
			}
			
			// 判斷使用哪種排序規則
			$no_prefix_tb_name = preg_replace("/^(ogs_|".CORE::$config["prefix"]."_)/", "", $tb_name,1);
			switch($no_prefix_tb_name){
				case "products":
				case "products_cate":
					$rule = $no_prefix_tb_name;
				break;
				case "new":
				case "download":
				case "agents":
					//$rule = 'cate';
				break;
				
				default:
					//$rule = 'default';
				break;
			}
			
			if(!empty($rule)){
				self::sort_tag($tb_name,$field_prefix,$id,$sort,$type);
				self::$rule($tb_name,$field_prefix);
			}else{
				self::$rs = false;
			}
		}
		
		// 寫入標記
		private static function sort_tag($tb_name,$field_prefix,$id,$sort,$type=false){
			if(!$type){ // 單項排序陣列化
				$id_array[0] = $id;
				$sort_array[0] = $sort;
			}else{
				$id_array = $id;
				$sort_array = $sort;
			}
			
			CHECK::is_array_exist($id_array);
			
			if(CHECK::is_pass()){
				foreach($id_array as $sort_key => $sort_id){
					$select = array(
						'table' => $tb_name,
						'field' => $field_prefix."_sort",
						'where' => $field_prefix."_id = '".$sort_id."'",
						//'order' => "",
						//'limit' => "",
					);
					
					$sql = DB::select($select);
					$rsnum = DB::num($sql);
					
					if(!empty($rsnum)){
						$row = DB::fetch($sql);
						$sort_tag = ($row[$field_prefix."_sort"] <= $sort_array[$sort_key])?-1:1;
						
						unset($tags);
						$tags["sort_tag"] = $sort_tag;
						$tags[$field_prefix."_id"] = $sort_id;
						$tags[$field_prefix."_sort"] = $sort_array[$sort_key];
						
						CRUD::U($tb_name,$tags);
					}
				}
			}else{
				self::$rs = false;
			}
		}
		
		// 取得排序所屬分類
		private static function cate_get($tb_name,$field_prefix){
			
			$select = array(
				'table' => $tb_name,
				'field' => $field_prefix."c_id",
				'where' => "sort_tag != '0'",
				//'order' => "",
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					$cate_array[] = $row[$field_prefix."c_id"];
				}
				
				CHECK::is_array_exist($cate_array);
				
				if(CHECK::is_pass()){
					$cate_array = array_flip($cate_array);
					$cate_array = array_flip($cate_array);
					
					return $cate_array;
				}else{
					self::$rs = false;
				}
			}
		}
		
		//---------------------------------------------------------------------------------
		
		// 產品排序
		private static function products($tb_name,$field_prefix){
			
			$pc_array = self::cate_get($tb_name,$field_prefix);
			
			if($pc_array !== false){
				foreach($pc_array as $pc_id){
					if(!self::$rs){
						break;
					}
					
					self::p_sort_handle($tb_name,$pc_id);
				}
			}else{
				self::$rs = false;
			}
		}
		
		// 處理排序
		private static function p_sort_handle($tb_name,$pc_id){
			
			$select = array(
				'table' => $tb_name,
				'field' => "p_id",
				'where' => "pc_id = '".$pc_id."'",
				'order' => "p_sort ".CORE::$config["sort"].",sort_tag desc",
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					unset($sort);
					$sort = array(
						"p_id" => $row["p_id"],
						"sort_tag" => 0,
						"p_sort" => ++$i,
					);
					
					CRUD::U($tb_name,$sort);
				}
				
				self::$rs = true;
			}else{
				self::$rs = false;
			}
		}
		
		//---------------------------------------------------------------------------------
		
		private static function pc_cate_get($tb_name){
			
			$select = array(
				'table' => $tb_name,
				'field' => "pc_parent",
				'where' => "sort_tag != '0'",
				//'order' => "",
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					$cate_array[] = $row["pc_parent"];
				}
				
				CHECK::is_array_exist($cate_array);
				
				if(CHECK::is_pass()){
					$cate_array = array_flip($cate_array);
					$cate_array = array_flip($cate_array);
					
					return $cate_array;
				}else{
					self::$rs = false;
				}
			}
		}
		
		// 產品分類排序
		private static function products_cate($tb_name,$field_prefix){
			
			$pc_array = self::pc_cate_get($tb_name);
			
			if($pc_array !== false){
				foreach($pc_array as $pc_id){
					if(!self::$rs){
						break;
					}
					
					self::pc_sort_handle($tb_name,$pc_id);
				}
			}else{
				self::$rs = false;
			}
		}
		
		// 處理排序
		private static function pc_sort_handle($tb_name,$pc_id){
		
			$select = array(
				'table' => $tb_name,
				'field' => "pc_id",
				'where' => "pc_parent = '".$pc_id."'",
				'order' => "pc_sort ".CORE::$config["sort"].",sort_tag desc",
				//'limit' => "",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					unset($sort);
					$sort = array(
						"pc_id" => $row["pc_id"],
						"sort_tag" => 0,
						"pc_sort" => ++$i,
					);
					
					CRUD::U($tb_name,$sort);
				}
				
				self::$rs = true;
			}else{
				self::$rs = false;
			}
		}
		
		//---------------------------------------------------------------------------------
		
	}
	

?>