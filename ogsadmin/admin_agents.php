<?php

	// 管理員設定
	class ADMIN_AGENTS extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				
				// AGENTS CATE
				case "cate":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-agents-cate-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::agents_cate();
				break;
				case "cate-add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-agents-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::agents_cate_add();
				break;
				case "cate-mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-agents-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::agents_cate_mod($args);
				break;
				case "cate-open":
				case "cate-close":
				case "cate-sort":
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::agents_cate_process($args);
				break;
				case "cate-replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::agents_cate_replace();
				break;
				/*
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::agents_cate_del($args);
				break;
				*/
				
				// AGENTS
				case "list":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-agents-list-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::agents_list($args);
				break;
				case "add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-agents-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::agents_add();
				break;
				case "mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-agents-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::agents_mod($args);
				break;
				case "open":
				case "close":
				case "sort":
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::agents_process($args);
				break;
				case "sk":
					CRUD::sk_handle($_REQUEST["sk"],CORE::$manage.'admin_agents/list/');
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::agents_replace();
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
		private function agents_cate(){
			$select = array (
				'table' => CORE::$config["prefix"].'_agents_cate',
				'field' => "*",
				'where' => '',
				'order' => 'agc_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_agents/cate/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_AGENTS_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_AGC_ID" => $row["agc_id"],
						"VALUE_AGC_SUBJECT" => $row["agc_subject"],
						"VALUE_AGC_SORT" => $row["agc_sort"],
						"VALUE_AGC_STATUS" => ($row["agc_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 分類新增
		private function agents_cate_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_AGC_TYPE" => 'add',
				"VALUE_AGC_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_agents_cate','agc'),
				"VALUE_ZONE_SELECT" => self::zone_select(),
				"TAG_DISABLE" => '',
			));
			
			CRUD::refill();
		}
		
		// 分類修改
		private function agents_cate_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_AGC_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_agents_cate',
				'field' => "*",
				'where' => "agc_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "zone_id":
							VIEW::assignGlobal("VALUE_ZONE_SELECT",self::zone_select($row["zone_id"]));
						break;
						case "agc_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				LANG::switch_make($row["lang_id"]);
			}
		}
		
		// 介紹頁各項處理
		private function agents_cate_process($args=false){
			switch(self::$func){
				case "cate-open":
					$rs = CRUD::status(CORE::$config["prefix"].'_agents_cate','agc',$_REQUEST["id"],1);
				break;
				case "cate-close":
					$rs = CRUD::status(CORE::$config["prefix"].'_agents_cate','agc',$_REQUEST["id"],0);
				break;
				case "cate-sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_agents_cate','agc',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "cate-del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_agents_cate','agc_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_agents_cate',array('agc_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_agents_cate','agc_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_agents_cate','agc',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 分類儲存
		public function agents_cate_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_agents_cate');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["agc_subject"]);
			CHECK::is_number($_REQUEST["agc_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["agc_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_agents/cate/';
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_agents/cate/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["agc_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["agc_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}

		// 地區選擇
		private static function zone_select($zone_id=false){
			
			if(CHECK::is_array_exist(CORE::$config["ag_zone"])){
				foreach(CORE::$config["ag_zone"] as $id => $zone){
					$selected = ($zone_id == $id)?'selected':'';
					$option_array[] = '<option value="'.$id.'" '.$selected.'>'.$zone.'</option>';
				}
				
				return implode("",$option_array);
			}
			
			CHECK::check_clear();
		}
		
		//--------------------------------------------------------------------------------------
		
		// 介紹頁列表
		private function agents_list($args=false){
			
			$sk = CRUD::sk_split($args[0]);
			
			foreach($sk as $field => $value){
				switch($field){
					case "agc_id":
						if(!empty($value)){
							$where .= 'where ag.'.$field."='".$value."'";
						}
					break;
				}
			}
			
			 $sql_str = "SELECT * FROM ".CORE::$config["prefix"]."_agents as ag 
						left join ".CORE::$config["prefix"]."_agents_cate as agc on agc.agc_id = ag.agc_id 
						".$where." order by ag.agc_id asc,ag.ag_sort ".CORE::$config["sort"]; 
			
			self::agents_cate_select($sk["agc_id"]);
			
			$sql = DB::select(false,$sql_str);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_agents/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_AGENTS_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_AG_ID" => $row["ag_id"],
						"VALUE_AGC_SUBJECT" => $row["agc_subject"],
						"VALUE_AG_SUBJECT" => $row["ag_subject"],
						"VALUE_AG_SORT" => $row["ag_sort"],
						"VALUE_AG_STATUS" => ($row["ag_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 介紹頁新增
		private function agents_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_AG_TYPE" => 'add',
				"VALUE_AG_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_agents','ag'),
				"TAG_DISABLE" => '',
			));
			
			self::agents_cate_select($_SESSION[CORE::$config["sess"]]["refill"]["agc_id"]);
			CRUD::refill();
		}

		// 更改介紹頁
		private function agents_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_AG_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_agents',
				'field' => "*",
				'where' => "ag_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				self::agents_cate_select($row["agc_id"]);
				
				foreach($row as $field => $value){
					switch($field){
						case "ag_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				LANG::switch_make($row["lang_id"]);
			}
		}
			
		// 介紹頁各項處理
		private function agents_process($args=false){
			switch(self::$func){
				case "open":
					$rs = CRUD::status(CORE::$config["prefix"].'_agents','ag',$_REQUEST["id"],1);
				break;
				case "close":
					$rs = CRUD::status(CORE::$config["prefix"].'_agents','ag',$_REQUEST["id"],0);
				break;
				case "sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_agents','ag',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_agents','ag_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_agents',array('ag_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_agents','ag_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_agents','ag',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 介紹頁儲存
		public function agents_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_agents');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["ag_subject"]);
			CHECK::is_must($_REQUEST["agc_id"]);
			CHECK::is_number($_REQUEST["ag_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["ag_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_agents/list/';
					break;
					case "mod":
						$crud_func = 'U';
						$_REQUEST["ag_content"] = addslashes($_REQUEST["ag_content"]);
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_agents/list/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["ag_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["ag_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}

		// 讀取分類選單
		private function agents_cate_select($agc_id=false){
			
			$select = array (
				'table' => CORE::$config["prefix"].'_agents_cate',
				'field' => "*",
				'where' => $where,
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_AGENTS_SELECT");
					VIEW::assign(array(
						"VALUE_AGC_ID" => $row["agc_id"],
						"VALUE_AGC_SUBJECT" => $row["agc_subject"],
						"VALUE_AGC_CURRENT" => ($agc_id == $row["agc_id"] && !empty($agc_id))?'selected':'',
					));
				}
			}
		}
		
		//--------------------------------------------------------------------------------------
		
	}

?>