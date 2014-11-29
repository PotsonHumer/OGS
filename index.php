<?php 
	
	class INDEX extends CORE{
		function __construct(){
			
			$temp_option = array(
				"HEADER" => 'ogs-index-header-tpl.html',
				"TOP" => 'ogs-top-tpl.html',
				"SIDE" => 'ogs-side-tpl.html',
				"FOOTER" => 'ogs-footer-tpl.html',
			);
			
			CORE::res_init('super_slide','box');
			new VIEW("ogs-index-tpl.html",$temp_option,false,false);
		}
	}
	
?>