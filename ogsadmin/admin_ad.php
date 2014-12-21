<?php

	// 廣告設定
	class ADMIN_AD extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				
				case "list":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-ad-list-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::ad_list($args);
				break;
				case "add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-ad-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::ad_add();
				break;
				case "mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-ad-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::ad_mod($args);
				break;
				case "open":
				case "close":
				case "sort":
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::ad_process($args);
				break;
				case "sk":
					CRUD::sk_handle($_REQUEST["sk"],CORE::$manage.'admin_ad/list/');
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::ad_replace();
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
			
		// 列表
		private function ad_list($args=false){
			
			$sk = CRUD::sk_split($args[0]);
			
			foreach($sk as $field => $value){
				switch($field){
					case "ad_cate":
						if(!empty($value)){
							$where = $field."='".$value."'";
						}
					break;
				}
			}
			
			$select = array(
				'table' => CORE::$config["prefix"].'_ad',
				'field' => "*",
				'where' => $where,
				//'order' => '',
				//'limit' => '',
			);
			
			self::ad_cate_select($sk["ad_cate"]);
			
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_ad/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_AD_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_AD_ID" => $row["ad_id"],
						"VALUE_ADC_SUBJECT" => CORE::$config["ad_cate"][$row["ad_cate"]],
						"VALUE_AD_SUBJECT" => $row["ad_subject"],
						"VALUE_AD_SORT" => $row["ad_sort"],
						"VALUE_AD_STATUS" => ($row["ad_status"])?'開啟':'關閉',
						"VALUE_AD_IMG" => CRUD::img_handle($row["ad_img"]),
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 新增
		private function ad_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_AD_TYPE" => 'add',
				"VALUE_AD_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_ad','ad'),
				"TAG_DISABLE" => '',
			));
			
			self::ad_cate_select($_SESSION[CORE::$config["sess"]]["refill"]["ad_cate"]);
			CRUD::refill();
		}

		// 更改
		private function ad_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_AD_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_ad',
				'field' => "*",
				'where' => "ad_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				self::ad_cate_select($row["ad_cate"]);
				
				foreach($row as $field => $value){
					switch($field){
						case "ad_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
						case "ad_img":
							$value = CRUD::img_handle($value);
						break;
						case "ad_content":
							$value = CORE::content_handle($value,true);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				LANG::switch_make($row["lang_id"]);
			}
		}
			
		// 各項處理
		private function ad_process($args=false){
			switch(self::$func){
				case "open":
					$rs = CRUD::status(CORE::$config["prefix"].'_ad','ad',$_REQUEST["id"],1);
				break;
				case "close":
					$rs = CRUD::status(CORE::$config["prefix"].'_ad','ad',$_REQUEST["id"],0);
				break;
				case "sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_ad','ad',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "del":
					if(!empty($args)){
						DB::delete(CORE::$config["prefix"].'_ad',array('ad_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						$rs = CRUD::delete(CORE::$config["prefix"].'_ad','ad',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 儲存
		public function ad_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_ad');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["ad_subject"],$_REQUEST["ad_cate"],$_REQUEST["ad_img"]);
			CHECK::is_number($_REQUEST["ad_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["ad_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_ad/list/';
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_ad/list/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["ad_type"] == "add"){
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
				
				if($_REQUEST["ad_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}

		// 讀取分類選單
		private function ad_cate_select($ad_cate=false){
			
			CHECK::is_array_exist(CORE::$config["ad_cate"]);
			
			if(CHECK::is_pass()){
				foreach(CORE::$config["ad_cate"] as $adc_id => $ad_cate_name){
					VIEW::newBlock("TAG_ADC_SELECT");
					VIEW::assign(array(
						"VALUE_ADC_ID" => $adc_id,
						"VALUE_ADC_SUBJECT" => $ad_cate_name,
						"VALUE_ADC_CURRENT" => (count(CORE::$config["ad_cate"]) == 1 || $ad_cate == $adc_id && !empty($ad_cate))?'selected':'',
					));
				}
			}
		}

		//--------------------------------------------------------------------------------------
		
	}

?>