<?php

	// 產品子項功能 (圖片、描述)
	class P_SUB{
		function __construct(){}
		
		// 圖片 - 列表
		public static function img_row($p_id,$tpl=true){
			
			$select = array (
				'table' => CORE::$config["prefix"].'_products_img',
				'field' => "*",
				'where' => "p_id = '".$p_id."'",
				'order' => "pi_sort asc",
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					if($tpl){
						VIEW::newBlock("TAG_IMG_LIST");
						foreach($row as $field => $value){
							if($field == "pi_img"){
								$value = CRUD::img_handle($value);
							}
							VIEW::assign("VALUE_".strtoupper($field),$value);
						}
					}else{
						$all_row[] = $row;
					}
				}
				
				return $all_row;
			}else{
				if($tpl){
					VIEW::newBlock("TAG_IMG_LIST");
				}
			}
		}
		
		// 圖片 - 儲存
		public static function img_save($p_id,$tb_name=false){
			
			if(empty($tb_name)){
				$tb_name = CORE::$config["prefix"].'_products_img';
			}
			
			if(empty($p_id)){
				$p_id = CRUD::$insert_id;
			}
			
			if(CHECK::is_array_exist($_REQUEST["pi_id"]) && CHECK::is_array_exist($_REQUEST["pi_img"])){
				foreach($_REQUEST["pi_id"] as $key => $pi_id){
					$replace = array(
						"pi_id" => $pi_id,
						"pi_sort" => $key + 1,
						"pi_img" => $_REQUEST["pi_img"][$key],
						"p_id" => $p_id,
					);
					
					if(CHECK::is_must($replace["pi_img"])){
						DB::replace($tb_name,$replace);
					}else{
						$input = array('pi_id' => $replace["pi_id"]);
						DB::delete($tb_name,$input);
					}
					
					CHECK::check_clear();
					
					if(!empty(DB::$error)){
						CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
						return false;
					}
				}
			}
			
			CHECK::check_clear();
		}
		
		//------------------------------------------------------------------------------------------
		
		// 描述 - 列表
		public static function desc_row($p_id){
			
			$select = array (
				'table' => CORE::$config["prefix"].'_products_desc',
				'field' => "*",
				'where' => "p_id = '".$p_id."'",
				'order' => "pd_sort asc",
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_PD_LIST");
					foreach($row as $field => $value){
						if($field == "pd_status"){
							VIEW::assign("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						}else{
							VIEW::assign("VALUE_".strtoupper($field),$value);
						}
						
						VIEW::assign("VALUE_PD_ROW",++$i);
					}
				}
			}else{
				VIEW::newBlock("TAG_PD_LIST");
				VIEW::assign(array(
					"VALUE_PD_ROW" => 1,
					"VALUE_PD_STATUS_CK0" => 'checked',
				));
			}
		}
		
		// 描述 - 儲存
		public static function desc_save($p_id,$tb_name=false){
			
			if(empty($tb_name)){
				$tb_name = CORE::$config["prefix"].'_products_desc';
			}
			
			if(empty($p_id)){
				$p_id = CRUD::$insert_id;
			}
			
			if(CHECK::is_array_exist($_REQUEST["pd_id"])){
				foreach($_REQUEST["pd_id"] as $key => $pd_id){
					$replace = array(
						"pd_id" => $pd_id,
						"pd_sort" => $_REQUEST["pd_sort"][$key],
						"pd_status" => $_REQUEST["pd_status"][$key],
						"pd_title" => $_REQUEST["pd_title"][$key],
						"pd_content" => addslashes($_REQUEST["pd_content"][$key]),
						"p_id" => $p_id,
					);
					
					if(CHECK::is_must($replace["pd_title"],$replace["pd_content"])){
						DB::replace($tb_name,$replace);
					}else{
						$input = array('pd_id' => $replace["pd_id"]);
						DB::delete($tb_name,$input);
					}
					
					CHECK::check_clear();
					
					if(!empty(DB::$error)){
						CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
						return false;
					}
				}
			}
			
			CHECK::check_clear();
		}
	}

?>