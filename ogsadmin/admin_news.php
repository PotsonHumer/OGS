<?php

	// 管理員設定
	class ADMIN_NEWS extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				
				// NEWS CATE
				case "cate":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-news-cate-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::news_cate();
				break;
				case "cate-add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-news-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::news_cate_add();
				break;
				case "cate-mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-news-cate-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::news_cate_mod($args);
				break;
				case "cate-open":
				case "cate-close":
				case "cate-sort":
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::news_cate_process($args);
				break;
				case "cate-replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::news_cate_replace();
				break;
				/*
				case "cate-del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::news_cate_del($args);
				break;
				*/
				
				// NEWS
				case "list":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-news-list-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"PAGE" => self::$temp.'ogs-admin-page-tpl.html',
					);
					self::news_list($args);
				break;
				case "add":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-news-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::news_add();
				break;
				case "mod":
					$temp_main = array(
						"MAIN" => self::$temp.'ogs-admin-news-form-tpl.html',
						"LEFT" => self::$temp.'ogs-admin-left-tpl.html',
						"SEO" => self::$temp.'ogs-admin-seo-tpl.html',
					);
					self::news_mod($args);
				break;
				case "open":
				case "close":
				case "sort":
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::news_process($args);
				break;
				case "sk":
					CRUD::sk_handle($_REQUEST["sk"],CORE::$manage.'admin_news/list/');
				break;
				case "replace":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::news_replace();
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
		private function news_cate(){
			$select = array (
				'table' => CORE::$config["prefix"].'_news_cate',
				'field' => "*",
				'where' => '',
				'order' => 'nc_sort '.CORE::$config["sort"],
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_news/cate/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_NEWS_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_NC_ID" => $row["nc_id"],
						"VALUE_NC_SUBJECT" => $row["nc_subject"],
						"VALUE_NC_SORT" => $row["nc_sort"],
						"VALUE_NC_STATUS" => ($row["nc_status"])?'開啟':'關閉',
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 分類新增
		private function news_cate_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_NC_TYPE" => 'add',
				"VALUE_NC_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_news_cate','nc'),
				"TAG_DISABLE" => '',
			));
			
			CRUD::refill();
		}
		
		// 分類修改
		private function news_cate_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_NC_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_news_cate',
				'field' => "*",
				'where' => "nc_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "nc_status":
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
		private function news_cate_process($args=false){
			switch(self::$func){
				case "cate-open":
					$rs = CRUD::status(CORE::$config["prefix"].'_news_cate','nc',$_REQUEST["id"],1);
				break;
				case "cate-close":
					$rs = CRUD::status(CORE::$config["prefix"].'_news_cate','nc',$_REQUEST["id"],0);
				break;
				case "cate-sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_news_cate','nc',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "cate-del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_news_cate','nc_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_news_cate',array('nc_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_news_cate','nc_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_news_cate','nc',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 分類儲存
		public function news_cate_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_news_cate',CORE::$config["prefix"].'_seo');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["nc_subject"]);
			CHECK::is_number($_REQUEST["nc_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["nc_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_news/cate/';
					break;
					case "mod":
						$crud_func = 'U';
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_news/cate/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["nc_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 儲存 SEO
					SEO::save($tb_array[1],$_REQUEST,$tb_array[0],'nc');
					
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["nc_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}
		
		// 分類刪除
		/*
		private function news_cate_del($args){
			$sql_args["ig_id"] = $args[0];
			$msg_path = CORE::$manage.'admin_news/cate/';
			
			if(CHECK::is_must($sql_args["nc_id"])){
				DB::delete(CORE::$config["prefix"].'_news_cate',$sql_args);
				
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
		*/
		
		//--------------------------------------------------------------------------------------
		
		// 介紹頁列表
		private function news_list($args=false){
			
			$sk = CRUD::sk_split($args[0]);
			
			foreach($sk as $field => $value){
				switch($field){
					case "nc_id":
						if(!empty($value)){
							$where .= 'where n.'.$field."='".$value."'";
						}
					break;
				}
			}
			
			 $sql_str = "SELECT * FROM ".CORE::$config["prefix"]."_news as n 
						left join ".CORE::$config["prefix"]."_news_cate as nc on nc.nc_id = n.nc_id 
						".$where." order by n.n_sort ".CORE::$config["sort"]; 
			
			self::news_cate_select($sk["nc_id"]);
			
			$sql = DB::select(false,$sql_str);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_news/list/');
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					
					VIEW::newBlock('TAG_NEWS_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_N_ID" => $row["n_id"],
						"VALUE_NC_SUBJECT" => $row["nc_subject"],
						"VALUE_N_SUBJECT" => $row["n_subject"],
						"VALUE_N_SORT" => $row["n_sort"],
						"VALUE_N_STATUS" => ($row["n_status"])?'開啟':'關閉',
						"VALUE_N_IMG" => CRUD::img_handle($row["n_img"]),
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}
		
		// 介紹頁新增
		private function news_add(){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '新增',
				"VALUE_N_TYPE" => 'add',
				"VALUE_N_SORT" => CRUD::max_sort(CORE::$config["prefix"].'_news','n'),
				"TAG_DISABLE" => '',
			));
			
			self::news_cate_select($_SESSION[CORE::$config["sess"]]["refill"]["nc_id"]);
			CRUD::refill();
		}

		// 更改介紹頁
		private function news_mod($args){
			
			VIEW::assignGlobal(array(
				"MSG_TITLE" => '修改',
				"VALUE_N_TYPE" => 'mod',
			));
			
			$select = array (
				'table' => CORE::$config["prefix"].'_news',
				'field' => "*",
				'where' => "n_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				self::news_cate_select($row["nc_id"]);
				
				foreach($row as $field => $value){
					switch($field){
						case "n_status":
							VIEW::assignGlobal("VALUE_".strtoupper($field).'_CK'.$value,'checked');
						break;
						case "n_img":
							$value = CRUD::img_handle($value);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				LANG::switch_make($row["lang_id"]);
				new SEO($row["seo_id"]);
			}
		}
			
		// 介紹頁各項處理
		private function news_process($args=false){
			switch(self::$func){
				case "open":
					$rs = CRUD::status(CORE::$config["prefix"].'_news','n',$_REQUEST["id"],1);
				break;
				case "close":
					$rs = CRUD::status(CORE::$config["prefix"].'_news','n',$_REQUEST["id"],0);
				break;
				case "sort":
					$rs = CRUD::sort(CORE::$config["prefix"].'_news','n',$_REQUEST["id"],$_REQUEST["sort"]);
				break;
				case "del":
					if(!empty($args)){
						SEO::del(CORE::$config["prefix"].'_news','n_id',$args[0]);
						DB::delete(CORE::$config["prefix"].'_news',array('n_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						SEO::del(CORE::$config["prefix"].'_news','n_id',$_REQUEST["id"]);
						$rs = CRUD::delete(CORE::$config["prefix"].'_news','n',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		// 介紹頁儲存
		public function news_replace($tb_array=false){
			
			if(!CHECK::is_array_exist($tb_array)){
				$tb_array = array(CORE::$config["prefix"].'_news',CORE::$config["prefix"].'_seo');
				CHECK::check_clear();
			}
			
			CHECK::is_must($_REQUEST["n_subject"]);
			CHECK::is_must($_REQUEST["nc_id"]);
			CHECK::is_number($_REQUEST["n_sort"]);
			
			if(CHECK::is_pass()){
				switch($_REQUEST["n_type"]){
					case "add":
						$crud_func = 'C';
						$_SESSION[CORE::$config["sess"]]['last_path'] = CORE::$manage.'admin_news/list/';
					break;
					case "mod":
						$crud_func = 'U';
						$_REQUEST["n_content"] = addslashes($_REQUEST["n_content"]);
					break;
					default:
						CORE::notice('失效的資訊',CORE::$manage.'admin_news/list/');
						return false;
					break;
				}
				
				// 執行 replace
				CRUD::$crud_func($tb_array[0],$_REQUEST);
				
				if(!empty(DB::$error)){
					CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
					
					if($_REQUEST["n_type"] == "add"){
						CRUD::refill(true);
					}
					
					return false;
				}else{
					// 儲存 SEO
					SEO::save($tb_array[1],$_REQUEST,$tb_array[0],'n');
					
					// 其他語系儲存
					if($crud_func == "C"){
						LANG::lang_sync($tb_array,$_REQUEST,__CLASS__,__FUNCTION__);
					}
				}
			}else{
				CORE::notice('參數錯誤',$_SESSION[CORE::$config["sess"]]['last_path']);
				
				if($_REQUEST["n_type"] == "add"){
					CRUD::refill(true);
				}
				
				return false;
			}
			
			CORE::notice('更新完成',$_SESSION[CORE::$config["sess"]]['last_path']);
		}

		// 讀取分類選單
		private function news_cate_select($nc_id=false){
			
			$select = array (
				'table' => CORE::$config["prefix"].'_news_cate',
				'field' => "*",
				'where' => $where,
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_NEWS_SELECT");
					VIEW::assign(array(
						"VALUE_NC_ID" => $row["nc_id"],
						"VALUE_NC_SUBJECT" => $row["nc_subject"],
						"VALUE_NC_CURRENT" => ($nc_id == $row["nc_id"] && !empty($nc_id))?'selected':'',
					));
				}
			}
		}
		
		//--------------------------------------------------------------------------------------
		
	}

?>