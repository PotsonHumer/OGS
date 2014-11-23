<?php

	// 管理員設定
	class ADMIN_INTRO extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "group-replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::intro_group_replace();
				break;
				case "group-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::intro_group_del($args);
				break;
				case "list":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-intro-list-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::intro_list();
				break;
				case "add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-intro-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
					);
					self::intro_add();
				break;
				case "mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-intro-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
					);
					self::intro_mod($args);
				break;
				case "open":
				case "close":
				case "sort":
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::intro_process($args);
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::intro_replace();
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-intro-group-tpl.html');
					self::intro_group();
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}

		//--------------------------------------------------------------------------------------
		
		// 介紹頁群組
		private function intro_group(){
			$select = array(
				'table' => CORE::$config["prefix"].'_intro_group',
				'field' => "*",
				//'where' => '',
				'order' => 'ig_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_IG_LIST");
					VIEW::assign(array(
						"VALUE_IG_ROW" => ++$i,
						"VALUE_IG_ID" => $row["ig_id"],
						"VALUE_IG_SORT" => $row["ig_sort"],
						"VALUE_IG_STATUS_CK".$row["ig_status"] => 'checked',
						"VALUE_IG_NAME" => $row["ig_name"],
						"VALUE_IG_DIR" => $row["ig_dir"],
						"VALUE_IG_DEL_PATH" => CORE::$manage.'admin_intro/group-del/'.$row["ig_id"].'/'
					));
				}
			}else{
				VIEW::newBlock("TAG_IG_LIST");
				VIEW::assign(array(
					"VALUE_IG_ROW" => 1,
					"VALUE_IG_SORT" => 1,
				));
			}
		}

		private function intro_group_replace(){
			
			$it_dir_str = "'".implode("','",$_REQUEST["ig_dir"])."'";
			CHECK::is_array_exist($_REQUEST["ig_id"]);
			eval("CHECK::is_letter($it_dir_str);");
			$msg_path = CORE::$manage.'admin_intro/';
			
			if(CHECK::is_pass()){
				
				$select = array(
					'table' => CORE::$config["prefix"].'_intro_group',
					'field' => "*",
					//'where' => '',
					//'order' => '',
					//'limit' => '',
				);
				
				$sql = DB::select($select);
				while($row = DB::field($sql)){
					$field_array[] = $row->name;
				}
				
				foreach($_REQUEST["ig_id"] as $key => $ID){
					unset($args);
					foreach($field_array as $field){
						$args[$field] = $_REQUEST[$field][$key];
					}
					
					DB::replace(CORE::$config["prefix"].'_intro_group',$args);
					
					if(!empty(DB::$error)){
						$msg_title = DB::$error;
					}else{
						$msg_title = '更新成功';
					}
				}
			}else{
				$msg_title = '參數錯誤';
			}
			
			CORE::notice($msg_title,$msg_path);
		}

		private function intro_group_del($args){
			$sql_args["ig_id"] = $args[0];
			$msg_path = CORE::$manage.'admin_intro/';
			
			if(CHECK::is_must($sql_args["ig_id"])){
				DB::delete(CORE::$config["prefix"].'_intro_group',$sql_args);
				
				if(!empty(DB::$error)){
					$msg_title = DB::$error;
				}else{
					$msg_title = '刪除成功';
				}
			}else{
				$msg_title = '參數錯誤';
			}
			
			CHECK::check_clear();
			CORE::notice($msg_title,$msg_path);
		}
		
		private function intro_group_select($ig_id=false,$output=true){
			$select = array(
				'table' => CORE::$config["prefix"].'_intro_group',
				'field' => "*",
				//'where' => '',
				'order' => 'ig_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					$selected = ($ig_id == $row["ig_id"] && !empty($ig_id))?'selected':'';
					
					if($output){
						VIEW::newBlock('TAG_IG_LIST');
						foreach($row as $field => $value){
							VIEW::assign("VALUE_".strtoupper($field),$value);
						}
	
						VIEW::assign("VALUE_IG_CURRENT",$selected);
					}
					
					if(!empty($selected) && !$output){
						return $row["ig_name"];
					}
				}
			}
		}
		
		//--------------------------------------------------------------------------------------
		
		// 介紹頁列表
		private function intro_list(){
			$select = array (
				'table' => CORE::$config["prefix"].'_intro',
				'field' => "*",
				'where' => '',
				'order' => 'it_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_intro/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					$ig_name = self::intro_group_select($row["ig_id"],false);
					
					VIEW::newBlock('TAG_INTRO_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_IT_ID" => $row["it_id"],
						"VALUE_IT_GROUP" => ($ig_name)?$ig_name:'無',
						"VALUE_IT_SUBJECT" => $row["it_subject"],
						"VALUE_IT_SORT" => $row["it_sort"],
						"VALUE_IT_STATUS" => ($row["it_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 介紹頁新增
		private function intro_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_IT_TYPE" => 'add',
				"VALUE_IT_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_intro','it'),
				"TAG_DISABLE" => '',
			));
			
			CRUD::refill();
			self::intro_group_select();
		}

		// 更改介紹頁
		private function intro_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_IT_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_intro',
				'field' => "*",
				'where' => "it_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				self::intro_group_select($row["ig_id"]);
				
				foreach($row as $field => $value){
					switch($field){
						case "it_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				LANG::switch_make($row["lang_id"]);
			}
		}
		
		// 介紹頁各項處理
		private function intro_process($args=false){
			switch(self::$func){
				case "open":
					$rs = CRUD::status(CORE::$config["prefix"].'_intro','it',$_REQUEST["id"],1);
				break;
				case "close":
					$rs = CRUD::status(CORE::$config["prefix"].'_intro','it',$_REQUEST["id"],0);
				break;
				case "sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_intro','it',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "del":
					if(!empty($args)){
						DB::delete(CORE::$config["prefix"].'_intro',array('it_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						$rs = CRUD::delete(CORE::$config["prefix"].'_intro','it',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 介紹頁儲存
		private function intro_replace(){
			CHECK::is_must($_REQUEST["it_subject"]);
			CHECK::is_number($_REQUEST["it_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["it_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_intro/list/';
					break;
					case "mod":
						$crud_func = 'U';
						$_REQUEST["it_content"] = addslashes($_REQUEST["it_content"]);
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_intro/list/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func(CORE::$config["prefix"].'_intro',$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["it_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["it_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}
		
		//--------------------------------------------------------------------------------------
		
	}

?>