<?php 
	
	class INDEX extends CORE{
		function __construct(){
			
			$temp_option = array(
				"HEADER" => 'ogs-header-tpl.html',
			);
			
			new VIEW("ogs-index-tpl.html",$temp_option,false,false);
		}
	}
	
?>