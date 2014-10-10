<?php

	include_once("admin_system.php");
	
	$index = new INDEX;
	
	class INDEX{
		function __construct(){
			global $login,$tpl;
			
			if(!empty($_REQUEST["am_submit"])){
				$login->login();
			}else{
				$login->reverify();
			}
			
			if($login->status){
				$tpl_path = 'temp/main_tpl.html';
				$this->tpl_load($tpl_path);
			}else{
				$tpl_path = 'temp/login_tpl.html';
				$this->tpl_load($tpl_path);
			}
			
			$tpl->printToScreen();
		}
		
	    function tpl_load($tpl_path=false){
	        global $tpl,$main;
			
			if($tpl_path){
		        $tpl = new TemplatePower($tpl_path);
		        //$tpl->assignInclude( "MAIN",'');
		        $tpl->prepare();
				$main->init_load();
				
				$tpl->assignGlobal("MSG_INDEX_WELCOME",'外觀我會慢慢用，目前先求有~~!');
				//$tpl->printToScreen();
			}
	    }
	}
	
?>