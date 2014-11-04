<?php

	class LIBS extends CORE{
		function __construct(){
			
			$self_path = CORE::real_path(__FILE__);
			
			$file_array = glob($self_path.'*.php');
			if(is_array($file_array) && count($file_array) > 1){
				foreach($file_array as $file_key => $file_path){
					if(!preg_match('/(summon.php)/',$file_path)){
						include_once $file_path;
					}
				}
			}
			
			/*
			include_once $self_path.'class.TemplatePower.inc.php';
			include_once $self_path.'db.php';
			include_once $self_path.'crud.php';
			include_once $self_path.'check.php';
			*/
		}
	}
	
	new LIBS;
?>