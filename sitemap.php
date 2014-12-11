<?php

	class SITEMAP{
		function __construct(){
			
			// 各項板模
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-left-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			PRODUCTS::show(true);
			
			$temp_main = array('MAIN' => 'ogs-sitemap-tpl.html');
			$temp_option = array_merge($temp_option,$temp_main);
			
			new VIEW("ogs-hull-tpl.html",$temp_option,false,false);
		}
	}
	

?>