<?php

	class CRUD extends DB{
		public static $rsnum;
		public static $call_class; // 回呼方法來源
		public static $call_func; // 回乎方法來源方法
		
		function __construct(){} // no need
		
		// 檢查輸入的參數是否符合資料庫
		// 如有多餘的參數則刪除
		private static function field_match($tb_name,array $args){
			
			$select = array(
				'table' => $tb_name,
				'field' => '*',
				//'where' => '',
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$args_keys = array_keys($args);
				
				while($row = DB::field($sql)){
					$rs = in_array($row->name,$args_keys);
					
					if($rs){
						$re_build[$row->name] = $args[$row->name];
					}
				}
				
				return $re_build;
			}else{
				return false;
			}
		}
		
		
		
		//-------------------------------------------------------------
		
		// Create
		public static function C($tb_name,array $args){
			$new_args = self::field_match($tb_name,$args);
			DB::insert($tb_name,$new_args);
		}
		
		// Updata
		public static function U($tb_name,array $args){
			$new_args = self::field_match($tb_name,$args);
			$new_args = array_reverse($new_args);
			DB::update($tb_name,$new_args);
		}
		
		// Read
		public static function R($tb_name,array $args,$custom_sql=false){
			
			if(!$custom_sql){
				if(is_array($args["where"])){
					foreach($args["where"] as $field => $value){
						$where_array[] = $field." = '".$value."'";
					}
	
					$where_str = implode(",",$where_array);
				}else{
					$where_str = $args["where"];
				}

				$select = array(
					'table' => $tb_name,
					'field' => $args["field"],
					'where' => $where_str,
					'order' => $args["order"],
					'limit' => $args["limit"],
				);

				$sql = DB::select($select);
			}else{
				$sql = DB::execute($custom_sql);
			}

			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					if(!empty($args["newBlock"])){
						VIEW::newBlock($args["newBlock"]);
						
						if(!empty(self::$call_class) && !empty(self::$call_func)){
							$call_class = self::$call_class;
							$call_func = self::$call_func;
							call_user_func($call_class::$call_func($row));
						}
						
						foreach($row as $field => $value){
							VIEW::assign("VALUE_".strtoupper($field),$value);
						}
						
						VIEW::assign("VALUE_ROW_NUM",++$i);
					}
					
					$all_row[] = $row;
				}

				CRUD::$rsnum = $rsnum;
				
				// 自動清除回呼方法
				self::$call_class = '';
				self::$call_func = '';
				
				return $all_row;
			}else{
				return false;
			}
		}
		
		// Delete
		public static function D($tb_name,array $args){
			
		}
	}
	

?>