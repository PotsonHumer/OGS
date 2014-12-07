<?php

	class EXHIBITION{
		
		protected static $pointer; // 指定參數
		protected static $func; // 功能參數
		protected static $seo = false; // seo 判定參數
		
		function __construct($args){
			
			self::$func = array_shift($args); // 取得功能參數
			
			self::$pointer = array_shift($args); // 頁面參數 , id or seo file name
			if(!is_numeric(self::$pointer) && !empty(self::$pointer)){
				self::$seo = true;
			}
			
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			switch(self::$func){
				case "detail":
					$temp_option = $temp_option + array("MAIN" => 'ogs-news-detail-tpl.html');
					NEWS::detail();
				break;
				default:
					$temp_option = $temp_option + array("MAIN" => 'ogs-exhibition-tpl.html',"PAGE" => 'ogs-page-tpl.html');
					NEWS::show(3);
				break;
			}
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
	}
?>