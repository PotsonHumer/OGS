<?php

	// 管理員設定
	class ADMIN_CONTACT extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::contact_process($args);
				break;
				case "detail":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-contact-detail-tpl.html');
					self::contact_detail($args);
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-contact-list-tpl.html');
					self::contact_list();
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}

		//--------------------------------------------------------------------------------------
		
		// 關於我們列表
		private function contact_list(){
			$select = array (
				'table' => 'ogs_contact',
				'field' => "*",
				//'where' => '',
				//'order' => "",
				//'limit' => '',
			);

			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_contact/');
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){

					VIEW::newBlock('TAG_CONTACT_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_CT_ID" => $row["ct_id"],
						"VALUE_CT_NAME" => $row["ct_name"],
						"VALUE_CT_COMPANY" => $row["ct_company"],
						"VALUE_CT_TEL" => $row["ct_tel"],
						"VALUE_CT_CELLPHONE" => $row["ct_cellphone"],
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}

		// 詳細顯示頁
		private function contact_detail($args){
			
			$select = array (
				'table' => 'ogs_contact',
				'field' => "*",
				'where' => "ct_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "ct_content":
							$value = CORE::content_handle($value,true);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
			}
		}
		
		// 介紹頁各項處理
		private function contact_process($args=false){
			switch(self::$func){
				case "del":
					if(!empty($args)){
						DB::delete('ogs_contact',array('ct_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						$rs = CRUD::delete('ogs_contact','ct',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		//--------------------------------------------------------------------------------------
		
	}

?>