<?php

	// 管理員設定
	class ADMIN_PRODUCTS extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				
				// CATE
				case "cate":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-products-cate-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::products_cate($args);
				break;
				case "cate-add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-products-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::products_cate_add();
				break;
				case "cate-mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-products-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::products_cate_mod($args);
				break;
				case "cate-open":
				case "cate-close":
				case "cate-sort":
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::products_cate_process($args);
				break;
				case "cate-replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::products_cate_replace();
				break;
				/*
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::products_cate_del($args);
				break;
				*/
				
				// Porducts
				case "list":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-products-list-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::products_list($args);
				break;
				case "add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-products-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"IMG" => self::$temp.'ogs-admin-products-img-tpl.html',
						"DESC" => self::$temp.'ogs-admin-products-desc-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::products_add();
				break;
				case "mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-products-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"IMG" => self::$temp.'ogs-admin-products-img-tpl.html',
						"DESC" => self::$temp.'ogs-admin-products-desc-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::products_mod($args);
				break;
				case "open":
				case "close":
				case "sort":
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::products_process($args);
				break;
				case "sk":
					CRUD::sk_handle($_REQUEST["sk"],CORE::$manage.'admin_products/list/');
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::products_replace();
				break;
				default:
					//$temp_main = array("MAIN" => self::$temp.'ogs-admin-intro-group-tpl.html');
					//self::intro_group();
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}

		//--------------------------------------------------------------------------------------
		
		// 分類列表
		private function products_cate($args){
			
			$sk = CRUD::sk_split($args[0]);
			$cate_option = CRUD::multi_layer_select(CORE::$config["prefix"].'_products_cate','pc',$sk["pc_id"]);
			VIEW::assignGlobal("TAG_PC_SELECT",$cate_option);
			
			$select = array (
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => "*",
				'where' => (!empty($sk["pc_id"]))?"pc_parent = '".$sk["pc_id"]."'":"pc_parent = '0'",
				'order' => 'pc_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_products/cate/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_PC_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_PC_ID" => $row["pc_id"],
						"VALUE_PC_NAME" => $row["pc_name"],
						"VALUE_PC_IMG" => CRUD::img_handle($row["pc_img"]),
						"VALUE_PC_SORT" => $row["pc_sort"],
						"VALUE_PC_STATUS" => ($row["pc_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 分類新增
		private function products_cate_add(){
			
			$cate_option = CRUD::multi_layer_select(CORE::$config["prefix"].'_products_cate','pc');
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_PC_TYPE" => 'add',
				"VALUE_PC_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_products_cate','pc'),
				"TAG_DISABLE" => '',
				"TAG_PC_SELECT" => $cate_option,
			));
			
			CRUD::refill();
		}
		
		// 分類修改
		private function products_cate_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_PC_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_products_cate',
				'field' => "*",
				'where' => "pc_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "pc_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				$cate_option = CRUD::multi_layer_select(CORE::$config["prefix"].'_products_cate','pc',$row["pc_parent"]);
				VIEW::assignGlobal("TAG_PC_SELECT",$cate_option);
				
				LANG::switch_make($row["lang_id"]);
				new SEO($row["seo_id"]);
			}
		}
		
		// 介紹頁各項處理
		private function products_cate_process($args=false){
			switch(self::$func){
				case "cate-open":
					$rs = CRUD::status(CORE::$config["prefix"].'_products_cate','pc',$_REQUEST["id"],1);
				break;
				case "cate-close":
					$rs = CRUD::status(CORE::$config["prefix"].'_products_cate','pc',$_REQUEST["id"],0);
				break;
				case "cate-sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_products_cate','pc',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "cate-del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_products_cate','pc_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_products_cate',array('pc_id' => $args[0]));
						$error_1 = DB::$error;

						DB::delete(CORE::$config["prefix"].'_products_cate',array('pc_parent' => $args[0]));
						$error_2 = DB::$error;
						if(!empty($error_1) || !empty($error_2)){
							CORE::notice($error_1.$error_2,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_products_cate','pc_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_products_cate','pc',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 分類儲存
		public function products_cate_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_products_cate',CORE::$config["prefix"].'_seo');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["pc_name"]);
			CHECK::is_number($_REQUEST["pc_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["pc_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_products/cate/';
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_products/cate/');
						return false;
					break;
				}
				
				// 執行 replace
				$_REQUEST["pc_img"] = CRUD::img_handle($_REQUEST["pc_img"]);
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["pc_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 儲存 SEO
					SEO::save($tb_array[1],$_REQUEST,$tb_array[0],'pc');
					
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["pc_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}
				
		//--------------------------------------------------------------------------------------
		
		// 介紹頁列表
		private function products_list($args=false){
			
			$sk = CRUD::sk_split($args[0]);
			$cate_option = CRUD::multi_layer_select(CORE::$config["prefix"].'_products_cate','pc',$sk["pc_id"]);
			VIEW::assignGlobal("TAG_PC_SELECT",$cate_option);
			
			foreach($sk as $field => $value){
				switch($field){
					case "pc_id":
						if(!empty($value)){
							$where .= 'where p.'.$field."='".$value."'";
						}
					break;
				}
			}
			
			 $sql_str = "SELECT * FROM ".CORE::$config["prefix"]."_products as p 
						left join ".CORE::$config["prefix"]."_products_cate as pc on pc.pc_id = p.pc_id 
						".$where." order by pc.pc_sort ".CORE::$config["sort"].",p.p_sort ".CORE::$config["sort"];
			
			$sql = DB::select(false,$sql_str);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_products/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_P_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_P_ID" => $row["p_id"],
						"VALUE_PC_NAME" => $row["pc_name"],
						"VALUE_P_NAME" => $row["p_name"],
						"VALUE_P_SORT" => $row["p_sort"],
						"VALUE_P_IMG" => $row["p_s_img"],
						"VALUE_P_STATUS" => ($row["p_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 介紹頁新增
		private function products_add(){
			
			$cate_option = CRUD::multi_layer_select(CORE::$config["prefix"].'_products_cate','pc');
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_P_TYPE" => 'add',
				"VALUE_P_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_products','p'),
				"TAG_DISABLE" => '',
				"TAG_PC_SELECT" => $cate_option,
			));
			
			P_SUB::img_row($row["p_id"]);
			P_SUB::desc_row($row["p_id"]);
			CRUD::refill();
		}

		// 更改介紹頁
		private function products_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_P_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_products',
				'field' => "*",
				'where' => "p_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "p_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				$cate_option = CRUD::multi_layer_select(CORE::$config["prefix"].'_products_cate','pc',$row["pc_id"]);
				VIEW::assignGlobal("TAG_PC_SELECT",$cate_option);
				
				LANG::switch_make($row["lang_id"]);
				P_SUB::img_row($row["p_id"]);
				P_SUB::desc_row($row["p_id"]);
				new SEO($row["seo_id"]);
			}
		}
			
		// 介紹頁各項處理
		private function products_process($args=false){
			switch(self::$func){
				case "open":
					$rs = CRUD::status(CORE::$config["prefix"].'_products','p',$_REQUEST["id"],1);
				break;
				case "close":
					$rs = CRUD::status(CORE::$config["prefix"].'_products','p',$_REQUEST["id"],0);
				break;
				case "sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_products','p',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_products','p_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_products',array('p_id' => $args[0]));
						DB::delete(CORE::$config["prefix"].'_products_img',array('p_id' => $args[0]));
						DB::delete(CORE::$config["prefix"].'_products_desc',array('p_id' => $args[0]));
						
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_products','p_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_products','p',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_products_img','p',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_products_desc','p',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 介紹頁儲存
		public function products_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(
					0 => CORE::$config["prefix"].'_products',
					1 => CORE::$config["prefix"].'_seo',
					2 => CORE::$config["prefix"].'_products_img',
					3 => CORE::$config["prefix"].'_products_desc',
				);
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["p_name"]);
			CHECK::is_number($_REQUEST["p_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["p_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_products/list/';
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_products/list/');
						return false;
					break;
				}
				
				// 執行 replace
				$_REQUEST["p_s_img"] = CRUD::img_handle($_REQUEST["p_s_img"]);
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["p_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 儲存 SEO
					SEO::save($tb_array[1],$_REQUEST,$tb_array[0],'p');
					
					// 儲存圖片
					P_SUB::img_save($_REQUEST["p_id"],$tb_array[2]);
					
					// 儲存描述
					P_SUB::desc_save($_REQUEST["p_id"],$tb_array[3]);
					
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["p_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}

		//--------------------------------------------------------------------------------------
		
	}

?>