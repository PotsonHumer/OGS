<?php

	class error{
		function __construct(){}
		
		function error_handle($code='',$msg='',$path=''){
			global $main_cfg,$main;
			
			switch($code){
				// 轉移至 $path 畫面
				case "goto":
					$main->js_notice($msg,$path);
				break;
					
				// 404警告畫面
				case "404":
					header("location: ".$main_cfg->root."404.html");
				break;
					
				// 帳號錯誤 -- 強制登出
				default:
					session_destroy();
					header("location: ".$main_cfg->url.$main_cfg->root);
					exit;
				break;
				
			}
		}
	}
	

?>