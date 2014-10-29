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
				'table' => 'ogs_admin',
				'fields' => "*",
				//'condition' => "",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::field($sql)){
					VIEW::newBlock("TAG_ADMIN_LIST");
					
					VIEW::assign("VALUE_OA_NUM",++$i);
					foreach($row as $field => $value){
						VIEW::assign('VALUE_'.strtoupper($field),$value);
					}
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
					$oa_row = parent::simple_load('ogs_admin',array('oa_id' => $oa_id));
					VIEW::newBlock("TAG_ORIGIN_PASSWORD");
					
					if(!$oa_row){
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
					
					DB::insert("ogs_admin",$sql_args);
					if(!empty(DB::$error)){
						CORE::notice(DB::$error,CORE::$config["manage"].'admin/');
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