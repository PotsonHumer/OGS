<?php

	// 詢價設定
	class ADMIN_INQUIRY extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::inquiry_process($args);
				break;
				case "detail":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-inquiry-detail-tpl.html');
					self::inquiry_detail($args);
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-inquiry-list-tpl.html');
					self::inquiry_list();
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}

		//--------------------------------------------------------------------------------------
		
		// 列表
		private function inquiry_list(){
			
			$select = array (
				'table' => 'ogs_inquiry',
				'field' => "*",
				//'where' => '',
				//'order' => "",
				//'limit' => '',
			);

			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_inquiry/');
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){

					VIEW::newBlock('TAG_INQUIRY_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_IQ_ID" => $row["iq_id"],
						"VALUE_IQ_NAME" => $row["iq_name"],
						"VALUE_IQ_COMPANY" => $row["iq_company"],
						"VALUE_IQ_TEL" => $row["iq_tel"],
						"VALUE_IQ_CELLPHONE" => $row["iq_cellphone"],
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}

		// 詳細顯示頁
		private function inquiry_detail($args){
			
			$select = array (
				'table' => 'ogs_inquiry',
				'field' => "*",
				'where' => "iq_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "iq_content":
							$value = CORE::content_handle($value,true);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				self::inquiry_items($row["iq_id"]);
			}
		}
		
		// 詢價產品列表
		private static function inquiry_items($iq_id){

			$select = array (
				'table' => 'ogs_inquiry_items',
				'field' => "*",
				'where' => "iq_id = '".$iq_id."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_IQI_LIST");
					self::inquiry_itmes_load($row);
				}
			}
		}
		
		// 取得詢價產品
		private static function inquiry_itmes_load(array $args){
			
			$select = array (
				'table' => $args["iqi_lang"].'_products',
				'field' => "*",
				'where' => "p_id = '".$args["p_id"]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				VIEW::assign(array(
					"VALUE_P_NAME" => $row["p_name"],
					"VALUE_P_S_IMG" => $row["p_s_img"],
					"VALUE_IQI_NUM" => $args["iqi_num"],
				));
			}
		}
		
		// 各項處理
		private function inquiry_process($args=false){
			switch(self::$func){
				case "del":
					if(!empty($args)){
						DB::delete('ogs_inquiry',array('iq_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						$rs = CRUD::delete('ogs_inquiry','iq',$_REQUEST["id"]);
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