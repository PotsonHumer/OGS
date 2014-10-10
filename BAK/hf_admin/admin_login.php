<?php	
	class login{
		function __construct(){}
		
		// 登入帳號
		function login(){
			global $db,$main_cfg,$error;
			
			$sql = $this->verify($_REQUEST["am_account"],$_REQUEST["am_password"]);
			
			if($this->status){
				$row = $db->field($sql);
				$_SESSION[$main_cfg->sess]["am_account"] = $row["am_account"];
				$_SESSION[$main_cfg->sess]["am_password"] = $row["am_password"];
				$_SESSION[$main_cfg->sess]["am_id"] = $row["am_id"];
				
				return true;
			}else{
				$error->error_handle();
				return false;
			}
		}
		
		// 登入後檢查帳號
		function reverify($switch=0){
			global $main_cfg,$error;
			
			if(!empty($_SESSION[$main_cfg->sess]["am_account"]) && !empty($_SESSION[$main_cfg->sess]["am_password"])){
				$this->verify($_SESSION[$main_cfg->sess]["am_account"],$_SESSION[$main_cfg->sess]["am_password"]);
			}else{
				$this->status = false;
			}
			
			if(!$this->status && !empty($switch)){
				$error->error_handle();
				exit;
			}
		}
		
		// 檢查帳號
		function verify($am_account=0,$am_password=0){
			global $db;
			
			if(empty($am_account) || empty($am_password)){
				$rsnum = 0;
			}else{
				$sql = $db->select(array(
					'table' => "admin_member",
					'fields' => "*",
					'condition' => "am_status = '1' and am_account = '".$am_account."' and am_password = '".$am_password."'"  //WHERE
					//'order' => "",
					//'limit' => 0
				));
				
				$rsnum = $db->num($sql);
			}
			
			$this->status = (!empty($rsnum))?true:false;
			return (!empty($sql))?$sql:"";
		}
		
		// 登出
		function logout(){
			global $main_cfg;
			session_destroy();
			
			header("location: ".$main_cfg->ad_root);
			exit;
		}
	}
?>