<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/core.php");

	$red = new redirect;
	
	class redirect{
		
		function __construct(){
			global $error;
			
			$this->basic = array("card","deck","member","comment");
			
			if(!empty($_SERVER["REQUEST_URI"])){
				
				$this->direct_split($_SERVER["REQUEST_URI"]);
			}else{
				$error->error_handle("404");
			}	
		}
		
		// 語系解析
		function lang_analyse($lang=''){
			global $main_cfg,$error;

			if(in_array($lang,$this->basic)){
				$main_cfg->lang = $main_cfg->all_lang[0];
				return true;
			}else{
				if(in_array($lang,$main_cfg->all_lang)){
					$main_cfg->lang = $lang;
					
					if(count($this->dir_array) == 1){
						unset($this->dir_array);
					}else{
						unset($this->dir_array[0]);
						$this->dir_array = array_values($this->dir_array);
					}
					return true;
				}
			}
			
			$error->error_handle("404");
			return false;
		}
		
		// 功能解析
		function func_analyse(){
			global $main_cfg,$error;
			
			foreach($this->dir_array as $key => $value){
				if($key == 0){
					$main_func = $value;
				}else{
					$main_cfg->parameter[] = $value;
				}
			}
			
			$rs_exist = file_exists($_SERVER['DOCUMENT_ROOT']."/".$main_func.".php");
			
			if($rs_exist){
				include_once($_SERVER['DOCUMENT_ROOT']."/".$main_func.".php"); // 載入程序
			}else{
				$error->error_handle("404");
			}
		}
		
		// 路徑拆解
		function direct_split($rui=''){
			global $main_cfg;
			
			$rui_array = explode("/",$rui);
			
			// 去除多餘 "/" 
			foreach($rui_array as $key => $value){
				if(!empty($value)){
					$this->dir_array[] = $value;
				}
			}
			
			// 語系解析
			if(is_array($this->dir_array)){
				$this->lang_analyse($this->dir_array[0]);
			}
			
			// 功能解析
			if(is_array($this->dir_array)){
				$this->func_analyse();
			}else{
				// 載入首頁
				//include_once($_SERVER['DOCUMENT_ROOT']."/index.php");
				include_once($_SERVER['DOCUMENT_ROOT']."/index.html");
			}
			//echo $main_cfg->lang;
		}
	}
	

?>