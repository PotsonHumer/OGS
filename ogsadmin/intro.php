<?php

	// 管理員設定
	class INTRO extends OGSADMIN{
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
						"VALUE_IG_DEL_PATH" => CORE::$config["manage"].'intro/group-del/'.$row["ig_id"].'/'
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
			$msg_path = CORE::$config["manage"].'intro/';
			
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
			$msg_path = CORE::$config["manage"].'intro/';
			
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
		
		private function intro_group_select($ig_id=false){
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
					VIEW::newBlock('TAG_IG_LIST');
					foreach($row as $field => $value){
						VIEW::assign("VALUE_".strtoupper($field),$value);
					}
					
					$selected = ($ig_id == $row["ig_id"] && !empty($ig_id))?'selected':'';
					VIEW::assign("VALUE_IG_CURRENT",$selected);
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
			$sql = PAGE::handle($sql, CORE::$config["manage"].'intro/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock('TAG_INTRO_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_IT_ID" => $row["it_id"],
						"VALUE_IT_SUBJECT" => $row["it_subject"],
						"VALUE_IT_SORT" => $row["it_sort"],
						"VALUE_IT_STATUS" => ($row["it_status"])?'開啟':'關閉',
					));
				}
			}
		}
		
		// 介紹頁新增
		private function intro_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_IT_TYPE" => 'add',
				"VALUE_IT_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_intro','it'),
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
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$config["manage"].'intro/list/';
						
						// 增加 lang_id
						$_REQUEST["lang_id"] = ++CORE::$lang_id;
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$config["manage"].'intro/list/');
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
		
		// 管理員列表
		private function admin_list(){
			// 非 root 登入不顯示root於列表
			$where_str = ($_SESSION[CORE::$config["sess"]]["ogsadmin"]["oa_id"] != "1")?"oa_id != '1'":'';
			
			$crud = array (
				'field' => "*",
				'where' => $where_str,
				//'order' => '',
				//'limit' => '',
				'newBlock' => 'TAG_ADMIN_LIST',
			);
			
			CRUD::$call_class = __CLASS__;
			CRUD::$call_func = 'admin_list_plus';
			$all_row = CRUD::R('ogs_admin',$crud);
		}
		
		public function admin_list_plus($row){
			if($row["oa_id"] == 1){
				VIEW::assign(array(
					"TAG_HIDE_CLOSE" => "hide",
					"TAG_HIDE_OPEN" => "hide"
				));
			}
			
			if($row["oa_status"]){
				$oa_status = '啟動';
				$oa_hide = 'TAG_HIDE_OPEN';
			}else{
				$oa_status = '關閉';
				$oa_hide = 'TAG_HIDE_CLOSE';
			}
			
			VIEW::assign(array(
				"TAG_OA_STATUS_STR" => $oa_status,
				$oa_hide => "hide",
			));
		}
		
		// 新增 , 修改管理員
		private function admin_mod($args=false){
			switch(self::$func){
				case "add":
					CRUD::refill();
					VIEW::assignGlobal('VALUE_OA_STATUS_CK1','checked');
					
					$func_title = '新增';
					$pass_title = '輸入密碼';
				break;
				case "mod":
					$func_title = '修改';
					$pass_title = '修改密碼';
					
					$oa_id = $args[0];
					$crud = array(
						'field' => "*",
						'where' => array('oa_id' => $oa_id),
						//'order' => '',
						//'limit' => '',
					);
					
					$all_row = CRUD::R('ogs_admin', $crud);
					foreach($all_row[0] as $field => $value){
						switch($field){
							case "oa_status":
								VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
							break;
							default:
								VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
							break;
						}
					}
					
					if($all_row[0]["oa_id"] == "1"){
						VIEW::assignGlobal("TAG_STATUS_DISABLE",'disabled');
					}
					
					VIEW::newBlock("TAG_ORIGIN_PASSWORD");

					if(empty(CRUD::$rsnum)){
						CORE::notice('查無此項目',CORE::$config["manage"].'admin',true);
					}
				break;
			}
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => $func_title,
				"MSG_PASSWORD" => $pass_title,
				"VALUE_OA_TYPE" => self::$func,
			));
		}

		// 狀態改變
		private function admin_status($args=false){
			CHECK::is_array_exist($args);
			CHECK::is_must($args[0]);
			
			if(CHECK::is_pass()){				
				switch(self::$func){
					case "open":
						$sql_args["oa_status"] = "1";
					break;
					case "close":
						$sql_args["oa_status"] = "0";
					break;
				}
				$sql_args["oa_id"] = $args[0];
				DB::update('ogs_admin',$sql_args);
			}
			
			if(!empty(DB::$error)){
				CORE::notice(DB::$error,CORE::$config["manage"].'admin/');
			}else{
				CORE::notice('更新成功',CORE::$config["manage"].'admin/',true);
			}
		}
		
		// 儲存
		private function admin_replace(){
			
			switch($_REQUEST["oa_type"]){
				case "add":
					CHECK::is_must($_REQUEST["oa_account"],$_REQUEST["oa_name"]);
					CHECK::is_email($_REQUEST["oa_email"]);
					CHECK::is_password($_REQUEST["oa_password"]);
					CHECK::is_same($_REQUEST["oa_password"], $_REQUEST["confirm_password"]);
					$sql_args["oa_createdate"] = date("Y-m-d H:i:s");
					$sql_args["oa_status"] = "1";
					
					$crud_func = 'C';
					$fail_path = CORE::$config["manage"].'admin/add/';
				break;
				case "mod":
					CHECK::is_must($_REQUEST["oa_account"],$_REQUEST["oa_name"]);
					CHECK::is_email($_REQUEST["oa_email"]);
					
					// 更改密碼
					if(!empty($_REQUEST["oa_password"])){
						// 檢查原本密碼
						$select = array(
							'table' => 'ogs_admin',
							'field' => '*',
							'where' => "oa_password = '".md5($_REQUEST["origin_password"])."' && oa_id = '".$_REQUEST["oa_id"]."'",
							//'order' => '',
							//'limit' => '',
						);
						
						$sql = DB::select($select);
						$old_password_check = DB::num($sql);
						
						CHECK::is_password($_REQUEST["oa_password"]);
						CHECK::is_must($old_password_check);
						
						// 確認新密碼沒有相同
						CHECK::is_same($_REQUEST["oa_password"], $_REQUEST["confirm_password"]);
						CHECK::is_not_same($_REQUEST["oa_password"], $_REQUEST["origin_password"]);
						
						$password_chage = true;
					}else{
						unset($_REQUEST["oa_password"]);
					}
					
					$crud_func = 'U';
					$fail_path = CORE::$config["manage"].'admin/mod/'.$_REQUEST["oa_id"].'/';
				break;
				default:
					$fail_msg = '失效的資訊';
					$fail_path = CORE::$config["manage"].'admin/';
					exit;
				break;
			}
				
			if(CHECK::is_pass()){
				foreach($_REQUEST as $field => $value){
					switch($field){
						case "oa_password":
							$sql_args[$field] = md5($value);
						break;
						default:
							$sql_args[$field] = $_REQUEST[$field];
						break;
					}
				}
				
				CRUD::$crud_func('ogs_admin', $sql_args);
			}else{
				$fail_msg = CHECK::$alert;
				$fail_act = true;
			}

			if(!empty(DB::$error)){
				$fail_msg = DB::$error;
				$fail_act = true;
			}
			
			if($fail_act){
				if($_REQUEST["oa_type"] == 'add'){
					CRUD::refill(true);
				}
				
				CORE::notice($fail_msg,$fail_path);
			}else{
				if($password_chage){
					CORE::full_logout();
				}else{
					CORE::notice('更新成功',CORE::$config["manage"].'admin/');
				}
			}
		}
	}

?>