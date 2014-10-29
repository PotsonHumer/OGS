<?php

	// 基本設定管理
	class SYSTEM extends OGSADMIN{
		static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-build-tpl.html');
					$temp_option = array_merge($temp_option,$temp_main);
				break;
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}
	}
	

?>