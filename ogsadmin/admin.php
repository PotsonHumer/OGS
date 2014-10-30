<?php

	// 管理員設定
	class ADMIN extends OGSADMIN{
		static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "add":
				case "mod":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-admin-form-tpl.html');
					self::admin_mod($args);
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::admin_replace();
				break;
				case "del":
					
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-admin-tpl.html');
					self::admin_list($args);
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}
		
		// 管理員列表
		private function admin_list(){
			
			$select = array (
				'field' => "*",
				//'where' => "",
				//'order' => '',
				//'limit' => '',
			);
			
			$all_row = CRUD::R('ogs_admin', $select);
			foreach($all_row as $row){
				$i++;
				VIEW::newBlock("TAG_ADMIN_LIST");
				foreach($row as $field => $value){
					VIEW::assign(array(
						"VALUE_".strtoupper($field) => $value,
						"VALUE_OA_NUM" => $i,
					));
				}
			}
		}
		
		// 新增、修改管理員
		private function admin_mod($args=false){
			
			switch(self::$func){
				case "add":
					$func_title = '新增';
					$pass_title = '輸入密碼';
				break;
				case "mod":
					$func_title = '修改';
					$pass_title = '修改密碼';
					
					$oa_id = $args[0];
					$select = array(
						'field' => "*",
						'where' => array('oa_id' => $oa_id),
						//'order' => '',
						//'limit' => '',
					);
					
					$all_row = CRUD::R('ogs_admin', $select);
					foreach($all_row[0] as $field => $value){
						VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
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
		
		// 儲存
		private function admin_replace(){			
			switch($_REQUEST["oa_type"]){
				case "add":
					$field_array = array('oa_account','oa_password','oa_name','oa_email','oa_createdate');
					foreach($field_array as $field){
						switch($field){
							case "oa_password":
								$sql_args[$field] = md5($_REQUEST[$field]);
							break;
							case "oa_createdate":
								$sql_args[$field] = date("Y-m-d H:i:s");
							break;
							default:
								$sql_args[$field] = $_REQUEST[$field];
							break;
						}
					}
					
					CHECK::is_email($sql_args["oa_email"]);
					CHECK::is_password($_REQUEST["oa_password"]);
					CHECK::is_same($sql_args["oa_password"], md5($_REQUEST["confirm_password"]));
					
					if(CHECK::is_pass()){
						DB::insert("ogs_admin",$sql_args);
					}else{
						DB::$error = CHECK::$alert;
					}
					
					if(!empty(DB::$error)){
						CORE::notice(DB::$error,CORE::$config["manage"].'admin/add/');
					}else{
						CORE::notice('新增成功',CORE::$config["manage"].'admin/');
					}
				break;
				case "mod":
				break;
				default:
					CORE::notice('失效的資訊',CORE::$config["manage"].'admin/',true);
				break;
			}
		}
	}

?>