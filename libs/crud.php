<?php

	class CRUD{
		public static $rsnum;
		public static $call_class; // 回呼方法來源
		public static $call_func; // 回乎方法來源方法
		
		function __construct(){} // no need
		
		// 檢查輸入的參數是否符合資料庫
		// 如有多餘的參數則刪除
		public static function field_match($tb_name,array $args){
			
			$args_keys = array_keys($args);
			
			$select = array(
				'table' => $tb_name,
				'field' => '*',
				//'where' => '',
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			while($row = DB::field($sql)){
				$rs = in_array($row->name,$args_keys);
				
				if($rs){
					$re_build[$row->name] = $args[$row->name];
				}
			}

			return $re_build;
		}
		
		// 重新填入表單欄位
		// $input : true => 輸入參數 , false => 填入參數
		public static function refill($input=false){
			if($input){
				$_SESSION[CORE::$config["sess"]]["refill"] = $_REQUEST;
			}else{
				CHECK::is_array_exist($_SESSION[CORE::$config["sess"]]["refill"]);
				if(CHECK::is_pass()){
					foreach($_SESSION[CORE::$config["sess"]]["refill"] as $field => $value){
						VIEW::assignGlobal('VALUE_'.strtoupper($field),$value);
					}
					
					unset($_SESSION[CORE::$config["sess"]]["refill"]);
				}
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
							$call_class::$call_func($row);
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
		
		// open , close
		public static function status($tb_name,$field_prefix,$id,$status){
			if(!CHECK::is_array_exist($id)){
				CORE::notice('請選擇項目',$_SESSION[CORE::$config["sess"]]['last_path'],true);
				return false;
			}
			
			foreach($id as $ID){
				$input = array(
					$field_prefix."_status" => $status,
					$field_prefix."_id" => $ID,
				);
				
				DB::update($tb_name,$input);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					return false;
				}
			}
			
			CHECK::check_clear();
			return true;
		}
		
		// sort
		public static function sort($tb_name,$field_prefix,$id,$sort){
			if(!CHECK::is_array_exist($id) || !CHECK::is_array_exist($sort)){
				CORE::notice('請選擇項目',$_SESSION[CORE::$config["sess"]]['last_path'],true);
				return false;
			}
			
			foreach($id as $key => $ID){
				$input = array(
					$field_prefix."_sort" => $sort[$key],
					$field_prefix."_id" => $ID,
				);
				
				DB::update($tb_name,$input);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					return false;
				}
			}
			
			CHECK::check_clear();
			return true;
		}
		
		// delete
		public static function delete($tb_name,$field_prefix,$id){
			if(!CHECK::is_array_exist($id)){
				CORE::notice('請選擇項目',$_SESSION[CORE::$config["sess"]]['last_path'],true);
				return false;
			}
			
			foreach($id as $ID){
				$input = array(
					$field_prefix."_id" => $ID,
				);
				
				DB::delete($tb_name,$input);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					return false;
				}
			}
			
			CHECK::check_clear();
			return true;
		}
		
		// 取得最大 sort 值
		public static function max_sort($tb_name,$field_prefix){
			$select = array(
				'table' => $tb_name,
				'field' => $field_prefix.'_sort',
				//'where' => '',
				'order' => $field_prefix.'_sort desc',
				'limit' => "0,1",
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				return $row[$field_prefix."_sort"] + 1;
			}else{
				return 1;
			}
		}
	}

?>