<?php

	// 管理員設定
	class ADMIN_DOWNLOAD extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				
				// DOWNLOAD CATE
				case "cate":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-download-cate-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::download_cate();
				break;
				case "cate-add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-download-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::download_cate_add();
				break;
				case "cate-mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-download-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::download_cate_mod($args);
				break;
				case "cate-open":
				case "cate-close":
				case "cate-sort":
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::download_cate_process($args);
				break;
				case "cate-replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::download_cate_replace();
				break;
				/*
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::download_cate_del($args);
				break;
				*/
				
				// DOWNLOAD
				case "list":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-download-list-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::download_list($args);
				break;
				case "add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-download-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::download_add();
				break;
				case "mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-download-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::download_mod($args);
				break;
				case "open":
				case "close":
				case "sort":
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::download_process($args);
				break;
				case "sk":
					CRUD::sk_handle($_REQUEST["sk"],CORE::$manage.'admin_download/list/');
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::download_replace();
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
		private function download_cate(){
			$select = array (
				'table' => CORE::$config["prefix"].'_download_cate',
				'field' => "*",
				'where' => '',
				'order' => 'dc_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_download/cate/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_DOWNLOAD_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_DC_ID" => $row["dc_id"],
						"VALUE_DC_SUBJECT" => $row["dc_subject"],
						"VALUE_DC_SORT" => $row["dc_sort"],
						"VALUE_DC_STATUS" => ($row["dc_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 分類新增
		private function download_cate_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_DC_TYPE" => 'add',
				"VALUE_DC_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_download_cate','dc'),
				"TAG_DISABLE" => '',
			));
			
			CRUD::refill();
		}
		
		// 分類修改
		private function download_cate_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_DC_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_download_cate',
				'field' => "*",
				'where' => "dc_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "dc_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				LANG::switch_make($row["lang_id"]);
				new SEO($row["seo_id"]);
			}
		}
		
		// 介紹頁各項處理
		private function download_cate_process($args=false){
			switch(self::$func){
				case "cate-open":
					$rs = CRUD::status(CORE::$config["prefix"].'_download_cate','dc',$_REQUEST["id"],1);
				break;
				case "cate-close":
					$rs = CRUD::status(CORE::$config["prefix"].'_download_cate','dc',$_REQUEST["id"],0);
				break;
				case "cate-sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_download_cate','dc',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "cate-del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_download_cate','dc_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_download_cate',array('dc_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_download_cate','dc_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_download_cate','dc',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 分類儲存
		public function download_cate_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_download_cate',CORE::$config["prefix"].'_seo');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["dc_subject"]);
			CHECK::is_number($_REQUEST["dc_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["dc_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_download/cate/';
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_download/cate/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["dc_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 儲存 SEO
					SEO::save($tb_array[1],$_REQUEST,$tb_array[0],'dc');
					
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["dc_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}
		
		//--------------------------------------------------------------------------------------
		
		// 介紹頁列表
		private function download_list($args=false){
			
			$sk = CRUD::sk_split($args[0]);
			
			foreach($sk as $field => $value){
				switch($field){
					case "dc_id":
						if(!empty($value)){
							$where .= 'where d.'.$field."='".$value."'";
						}
					break;
				}
			}
			
			$sql_str = "SELECT * FROM ".CORE::$config["prefix"]."_download as d 
						left join ".CORE::$config["prefix"]."_download_cate as dc on dc.dc_id = d.dc_id 
						".$where." order by d.d_sort ".CORE::$config["sort"]; 
			
			self::download_cate_select($sk["dc_id"]);
			
			$sql = DB::select(false,$sql_str);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_download/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_DOWNLOAD_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_D_ID" => $row["d_id"],
						"VALUE_DC_SUBJECT" => $row["dc_subject"],
						"VALUE_D_SUBJECT" => $row["d_subject"],
						"VALUE_D_SORT" => $row["d_sort"],
						"VALUE_D_STATUS" => ($row["d_status"])?'開啟':'關閉',
						"VALUE_D_IMG" => CRUD::img_handle($row["d_img"]),
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 介紹頁新增
		private function download_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_D_TYPE" => 'add',
				"VALUE_D_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_download','d'),
				"TAG_DISABLE" => '',
			));
			
			self::download_cate_select($_SESSION[CORE::$config["sess"]]["refill"]["dc_id"]);
			
			VIEW::newBlock("TAG_FILE_LIST");
			CRUD::refill();
		}

		// 更改介紹頁
		private function download_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_D_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_download',
				'field' => "*",
				'where' => "d_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				self::download_cate_select($row["dc_id"]);
				
				foreach($row as $field => $value){
					switch($field){
						case "d_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
						case "d_img":
							$value = CRUD::img_handle($value);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				if(!empty($row["d_file"])){
					$d_file_array = unserialize($row["d_file"]);
					foreach($d_file_array as $file_name => $file){
						VIEW::newBlock("TAG_FILE_LIST");
						VIEW::assign(array(
							"VALUE_D_FILE_NAME" => $file_name,
							"VALUE_D_FILE" => $file,
						));
					}
				}
				
				LANG::switch_make($row["lang_id"]);
				//new SEO($row["seo_id"]);
			}
		}
			
		// 介紹頁各項處理
		private function download_process($args=false){
			switch(self::$func){
				case "open":
					$rs = CRUD::status(CORE::$config["prefix"].'_download','d',$_REQUEST["id"],1);
				break;
				case "close":
					$rs = CRUD::status(CORE::$config["prefix"].'_download','d',$_REQUEST["id"],0);
				break;
				case "sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_download','d',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_download','d_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_download',array('d_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_download','d_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_download','d',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 介紹頁儲存
		public function download_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_download',CORE::$config["prefix"].'_seo');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["d_subject"]);
			CHECK::is_must($_REQUEST["dc_id"]);
			CHECK::is_number($_REQUEST["d_sort"]);
			
			if(CHECK::is_pass()){
				
				if(is_array($_REQUEST["d_file"]) && is_array($_REQUEST["d_file_name"])){
					foreach($_REQUEST["d_file"] as $key => $file_path){
						$file_group[$_REQUEST["d_file_name"][$key]] = $file_path;
					}
					
					$d_file_str = serialize($file_group);
					unset($_REQUEST["d_file"],$_REQUEST["d_file_name"],$file_group);
					$_REQUEST["d_file"] = $d_file_str;
				}
				
				switch($_REQUEST["d_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_download/list/';
					break;
					case "mod":
						$crud_func = 'U';
						//$_REQUEST["d_content"] = addslashes($_REQUEST["d_content"]);
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_download/list/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["d_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 儲存 SEO
					//SEO::save($tb_array[1],$_REQUEST,$tb_array[0],'d');
					
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["d_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}

		// 讀取分類選單
		private function download_cate_select($dc_id=false){
			
			$select = array (
				'table' => CORE::$config["prefix"].'_download_cate',
				'field' => "*",
				'where' => $where,
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_DOWNLOAD_SELECT");
					VIEW::assign(array(
						"VALUE_DC_ID" => $row["dc_id"],
						"VALUE_DC_SUBJECT" => $row["dc_subject"],
						"VALUE_DC_CURRENT" => ($dc_id == $row["dc_id"] && !empty($dc_id))?'selected':'',
					));
				}
			}
		}
		
		//--------------------------------------------------------------------------------------
		
	}

?>