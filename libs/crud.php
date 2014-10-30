<?php

	class CRUD extends DB{
		public static $rsnum;
		
		function __construct(){} // no need
		
		// Create
		public static function C($tb_name,array $args){
			
		}
		
		// Read
		public static function R($tb_name,array $args,$custom_sql=false){
			
			if(!$custom_sql){
				if(is_array($args["where"])){
					foreach($args["where"] as $field => $value){
						$where_array[] = $field." = '".$value."'";
					}
	
					$where_str = implode(",",$where_array);
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
				while($row = DB::field($sql)){
					$all_row[] = $row;
				}

				CRUD::$rsnum = $rsnum;
				return $all_row;
			}else{
				return false;
			}
		}
		
		// Updata
		public static function U($tb_name,array $args){
			
		}
		
		// Delete
		public static function D($tb_name,array $args){
			
		}
	}
	

?>