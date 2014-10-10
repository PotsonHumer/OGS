<?php

	include_once("admin_system.php");
	
	class error{
		function __construct(){}
		
		// 錯誤處理
		function error_handle($code='',$msg=0,$val=0){
			global $main_cfg;
			
			switch($code){
				// 轉移至 $val 畫面
				case "goto":
				break;
					
				// 帳號錯誤 -- 強制登出
				default:
					session_destroy();
					header("location: ".$main_cfg->url.$main_cfg->ad_root);
					exit;
				break;
				
			}
		}
	}	
?>
